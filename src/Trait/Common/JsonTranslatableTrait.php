<?php

namespace App\Trait\Common;

trait JsonTranslatableTrait
{
    public function getTranslation(string $field, string $lang, ?string $fallbackLang = 'en'): ?string
    {
        $translationsField = $field . '_translations';
        if (!property_exists($this, $translationsField)) {
            return null;
        }
        
        // ✅ Ensure we have an array
        $translations = $this->$translationsField ?? [];
        if (!is_array($translations)) {
            $translations = [];
        }
        
        return $translations[$lang] ?? $translations[$fallbackLang] ?? null;
    }
    
    public function setTranslation(string $field, string $lang, ?string $value): static
    {
        $translationsField = $field . '_translations';
        if (!property_exists($this, $translationsField)) {
            throw new \InvalidArgumentException("Translation field {$translationsField} does not exist");
        }
        
        // ✅ Ensure we have an array
        if (!is_array($this->$translationsField ?? null)) {
            $this->$translationsField = [];
        }
        
        $this->$translationsField[$lang] = $value;
        return $this;
    }
    
    public function getAllTranslations(string $field): array
    {
        $translationsField = $field . '_translations';
        return $this->$translationsField ?? [];
    }

    /**
     * Magic method to handle dynamic translation getters and setters
     * Supports: getNameHu(), setTitleEn(), getMetaDescDe(), etc.
     */
    public function __call(string $method, array $args)
    {
        // Match: getNameHu, setTitleEn, getMetaDescDe, getShortDescEs (NO underscore)
        if (preg_match('/^(get|set)([A-Z][a-zA-Z]+)([A-Z][a-z]{1,2})$/', $method, $matches)) {
            $action = $matches[1]; // 'get' or 'set'
            $fieldCamelCase = $matches[2]; // 'Name', 'Title', 'MetaDesc', 'ShortDesc', etc.
            $lang = strtolower($matches[3]); // 'Hu' -> 'hu', 'En' -> 'en'
            
            // Convert to snake_case: Name -> name, MetaDesc -> meta_desc
            $field = $this->camelToSnake($fieldCamelCase);
            
            // Verify translation property exists
            $translationsProperty = $field . '_translations';
            if (!property_exists($this, $translationsProperty)) {
                throw new \BadMethodCallException(
                    "Translation field '{$translationsProperty}' does not exist on " . get_class($this)
                );
            }
            
            // ✅ NEW: Check if property is initialized for typed properties
            try {
                $reflection = new \ReflectionProperty($this, $translationsProperty);
                $reflection->setAccessible(true);
                
                // For typed properties, check if initialized
                if ($reflection->hasType() && !$reflection->isInitialized($this)) {
                    // Initialize with empty array
                    $reflection->setValue($this, []);
                }
            } catch (\ReflectionException $e) {
                // Property doesn't exist, already handled above
            }
            
            // Get or set translation
            if ($action === 'get') {
                return $this->getTranslation($field, $lang);
            } else {
                return $this->setTranslation($field, $lang, $args[0] ?? null);
            }
        }

        throw new \BadMethodCallException("Method {$method} does not exist on " . get_class($this));
    }

    /**
     * Helper method to convert CamelCase to snake_case
     */
    private function camelToSnake(string $input): string
    {
        // Name -> name
        // MetaDesc -> meta_desc
        // ShortDesc -> short_desc
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
}
