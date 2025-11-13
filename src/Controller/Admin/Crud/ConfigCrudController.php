<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Config;
use App\Service\Admin\CrudService;
use App\Service\Modules\ImageService;
use App\Entity\Category;
use App\Service\Modules\LangService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use App\Service\Modules\TranslateService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigCrudController extends AbstractCrudController
{
    private string $lang;

    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private ImageService $imageService,
        private readonly CrudService $crudService,
        private readonly LangService $langService,
        private readonly TranslateService $translateService,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
        $this->lang = $this->langService->getDefault();
        if($this->requestStack->getCurrentRequest()){
            $locale = $this->requestStack->getCurrentRequest()->getSession()->get('_locale');
            if($locale){
                $this->lang = $this->requestStack->getCurrentRequest()->getSession()->get('_locale');
                $this->translateService->setLangs($this->lang);
                $this->langService->setLang($this->lang);
            }
        }
    }
    
    public static function getEntityFqcn(): string
    {
        return Config::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Config) return;

        $this->setImages($entityInstance);

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Config) return;

        $this->setImages($entityInstance);

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        
        $this->getContext()->getRequest()->setLocale($this->lang);
        $this->translator->getCatalogue($this->lang);
        $this->translator->setLocale($this->lang);
        
        /**
         * on forms
         */
        yield FormField::addTab($this->translateService->translateWords("options"));
            yield AssociationField::new('schema_type', "Schema ".$this->translateService->translateWords("schema_type",'type'))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex()
                ->setFormTypeOption('attr', [
                    'data-config-target' => 'typeField',
                    'data-action' => 'change->config#changeType'
                ]);
            yield TextField::new('email', $this->translateService->translateWords("email"))
                ->hideOnIndex();
            yield TextField::new('address', $this->translateService->translateWords("address"))
                ->hideOnIndex();
            yield TextField::new('city', $this->translateService->translateWords("city"))
                ->hideOnIndex();
            yield TextField::new('zip_code', $this->translateService->translateWords("zip_code", "zip code"))
                ->hideOnIndex();
            yield TextField::new('phone', $this->translateService->translateWords("phone"))
                ->hideOnIndex();
            yield TextareaField::new('schema_text', "Schema ".$this->translateService->translateWords("text","text"))
                ->setFormTypeOption('row_attr', ['data-config-target' => 'schemaTextRow'])
                ->hideOnIndex();
            yield Field::new('favicon', 'Favicon')
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                ])
                ->onlyOnForms();
            yield Field::new('apple_icon', 'Apple icon')
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                ])
                ->onlyOnForms();

        // ✅ Default language tab - use custom getter/setter
        yield FormField::addTab($this->translateService->translateWords($this->langService->getDefaultObject()->getName()));
            yield TextField::new('title', $this->translateService->translateWords("title"))
                ->setFormTypeOption('getter', function(Config $entity) {
                    return $entity->getTitle($this->langService->getDefault());
                })
                ->setFormTypeOption('setter', function(Config &$entity, $value) {
                    $entity->setTitle($value, $this->langService->getDefault());
                })
                ->hideOnIndex();
            yield TextField::new('meta_desc', $this->translateService->translateWords("meta_desc","meta desc"))
                ->setFormTypeOption('getter', function(Config $entity) {
                    return $entity->getMetaDesc($this->langService->getDefault());
                })
                ->setFormTypeOption('setter', function(Config &$entity, $value) {
                    $entity->setMetaDesc($value, $this->langService->getDefault());
                })
                ->hideOnIndex();
        
        // ✅ Other language tabs - use custom getter/setter for each
        foreach($this->langService->getLangs() as $lang){
            if(!$lang->isDefault()){
                $langCode = $lang->getCode();
                
                yield FormField::addTab($this->translateService->translateWords($lang->getName()));
                
                yield TextField::new('title_' . $langCode, $this->translateService->translateWords("title"))
                    ->setFormTypeOption('getter', function(Config $entity) use ($langCode) {
                        return $entity->getTitle($langCode);
                    })
                    ->setFormTypeOption('setter', function(Config &$entity, $value) use ($langCode) {
                        $entity->setTitle($value, $langCode);
                    })
                    ->hideOnIndex();
                    
                yield TextField::new('meta_desc_' . $langCode, $this->translateService->translateWords("meta_desc","meta desc"))
                    ->setFormTypeOption('getter', function(Config $entity) use ($langCode) {
                        return $entity->getMetaDesc($langCode);
                    })
                    ->setFormTypeOption('setter', function(Config &$entity, $value) use ($langCode) {
                        $entity->setMetaDesc($value, $langCode);
                    })
                    ->hideOnIndex();
            }
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('admin/crud/config_crud_form_theme.html.twig')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->addFormTheme('admin/form/image_upload_with_preview.html.twig')
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig');
    }

    public function setImages($entityInstance){
        $favicon = $this->getContext()->getRequest()->files->get('Config')['favicon'] ?? null;
        $apple_icon = $this->getContext()->getRequest()->files->get('Config')['apple_icon'] ?? null;

        $this->imageService->setFile($favicon);
        $favicon_ico = $this->imageService->faviconUpload("favicon");
        $favicon_image = $this->imageService->simpleUpload("favicon");
        $entityInstance->setFavicon($favicon_ico);
        
        $this->imageService->setFile($apple_icon);
        $apple_icon = $this->imageService->simpleUpload("apple_image");
        $entityInstance->setAppleIcon($apple_icon);
    }
}
