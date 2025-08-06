<?php 

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ImageService
{
    
    private \GdImage|false $originalImage;
    private UploadedFile|null $file;
    private string $originalWebpPath;
    private string $croppedWebpPath;

    public function __construct(
        private readonly ParameterBagInterface $params
    ) 
    {

    }

    public function setFile($file): static
    {
        $this->file = $file;
        return $this;
    }

    public function upload(string $dir): string
    {
        if ($this->file) {
            $this->originalImage = imagecreatefromstring(file_get_contents($this->file->getRealPath()));
            if (!$this->originalImage) {
                throw new \RuntimeException('Invalid image file.');
            }

            $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
            $slugger = new AsciiSlugger();
            $safeName = strtolower($slugger->slug($originalFilename));
            $uniqueSuffix = bin2hex(random_bytes(4)); // 8-character string
            $baseName = $safeName . '_' . $uniqueSuffix;

            $targetDir = $this->params->get('kernel.project_dir') . '/public/uploads/'.$dir;

            $this->originalWebpPath = "$targetDir/{$baseName}.webp";
            $this->croppedWebpPath = "$targetDir/{$baseName}_cropped.webp";

            // Convert to WEBP (original)
            imagewebp($this->originalImage, $this->originalWebpPath, 90);

            // Optional: Free original image resource
            imagedestroy($this->originalImage);

            // Reload for cropping
            $this->originalImage = imagecreatefromwebp($this->originalWebpPath);

            return $baseName;
        }

        return "";
    }
    
    public function crop(): void
    {
        $targetWidth = (int) $_ENV['BLOG_W']; // 400
        $targetHeight = (int) $_ENV['BLOG_H']; // 400

        $srcWidth = imagesx($this->originalImage);
        $srcHeight = imagesy($this->originalImage);

        // Compute side length of largest centered square
        $side = min($srcWidth, $srcHeight);
        $cropX = (int)(($srcWidth - $side) / 2);
        $cropY = (int)(($srcHeight - $side) / 2);

        // Crop to square
        $cropped = imagecrop($this->originalImage, [
            'x' => $cropX,
            'y' => $cropY,
            'width' => $side,
            'height' => $side,
        ]);

        if ($cropped) {
            // Resize to 400x400
            $resized = imagescale($cropped, $targetWidth, $targetHeight, IMG_BICUBIC);

            if ($resized) {
                imagewebp($resized, $this->croppedWebpPath, 90);
                imagedestroy($resized);
            }

            imagedestroy($cropped);
        }

        imagedestroy($this->originalImage);
    }

    public function processImage(UploadedFile|null $file, $entityInstance): void
    {
        $this->setFile($file);

        if ($file) {
            $filename = $this->upload("blog");
            if($filename){
                $this->crop();
            }
            $entityInstance->setImage($filename);
        }
    }
}