<?php 

namespace App\Service\Modules;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Entity\Slide;
use App\Entity\GalleryImage;
use PHP_ICO;
use Imagick;

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

    public function faviconUpload(string $dir): string
    {
        if ($this->file) {
            // Read image file
            $imageData = file_get_contents($this->file->getRealPath());
            if (!$imageData) {
                throw new \RuntimeException('Invalid image file.');
            }

            // Prepare file name
            $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
            $slugger = new AsciiSlugger();
            $safeName = strtolower($slugger->slug($originalFilename));
            $uniqueSuffix = bin2hex(random_bytes(4)); // 8-character
            $baseName = $safeName . '_' . $uniqueSuffix;

            $targetDir = $this->params->get('kernel.project_dir') . '/public/uploads/' . $dir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $icoFilePath = $targetDir . '/' . $baseName . '.ico';

            // Convert and save ICO file
            $ico = new PHP_ICO($this->file->getRealPath());
            $ico->save_ico($icoFilePath);

            return $baseName . '.ico';
        }

        return "";
    }

    public function simpleUpload(string $dir): string
    {
        if ($this->file) {
            // Validate and create image resource
            $this->originalImage = imagecreatefromstring(file_get_contents($this->file->getRealPath()));
            if (!$this->originalImage) {
                throw new \RuntimeException('Invalid image file.');
            }

            // Prepare safe, unique filename
            $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION));
            $slugger = new AsciiSlugger();
            $safeName = strtolower($slugger->slug($originalFilename));
            $uniqueSuffix = bin2hex(random_bytes(4)); // 8-character string
            $baseName = $safeName . '_' . $uniqueSuffix . '.' . $extension;

            // Setup target directory
            $targetDir = $this->params->get('kernel.project_dir') . '/public/uploads/' . $dir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Move the uploaded file
            move_uploaded_file($this->file->getRealPath(), $targetDir . '/' . $baseName);

            // Return relative upload path for web access
            return $baseName;
        }

        // Return empty string if upload failed or no file
        return "";
    }

    public function upload(string $dir): string
    {
        if ($this->file) {
            $extension = strtolower(pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION));
            $filePath = $this->file->getRealPath();
            $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);

            if (!$filePath || !file_exists($filePath)) {
                $filePath = "uploads/{$dir}/{$originalFilename}.{$extension}";
                //throw new \RuntimeException('Uploaded file is missing or path is invalid.');
            }

            if ($extension === 'webp') {
                $this->originalImage = imagecreatefromwebp($filePath);
            } elseif ($extension === 'heic' || $extension === 'heif') {
                if (!extension_loaded('imagick')) {
                    throw new \RuntimeException('Imagick extension is required to process HEIC images.');
                }
                // Load HEIC via Imagick and convert to GD image resource
                $imagick = new Imagick();
                $imagick->readImage($filePath);
                $imagick->setImageFormat('png'); // convert HEIC to PNG in memory
                $imageBlob = $imagick->getImageBlob();
                $this->originalImage = imagecreatefromstring($imageBlob);
                $imagick->clear();
                $imagick->destroy();

            } else {
                $this->originalImage = imagecreatefromstring(file_get_contents($filePath));
            }

            if (!$this->originalImage) {
                throw new \RuntimeException('Invalid image file.');
            }

            if (!imageistruecolor($this->originalImage)) {
                $trueColorImage = imagecreatetruecolor(imagesx($this->originalImage), imagesy($this->originalImage));
                imagealphablending($trueColorImage, false);
                imagesavealpha($trueColorImage, true);
                imagecopy($trueColorImage, $this->originalImage, 0, 0, 0, 0, imagesx($this->originalImage), imagesy($this->originalImage));
                imagedestroy($this->originalImage);
                $this->originalImage = $trueColorImage;
            }

            $slugger = new AsciiSlugger();
            $safeName = strtolower($slugger->slug($originalFilename));
            $uniqueSuffix = bin2hex(random_bytes(4));
            $baseName = $safeName . '_' . $uniqueSuffix;

            $targetDir = $this->params->get('kernel.project_dir') . '/public/uploads/' . $dir;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $this->originalWebpPath = "$targetDir/{$baseName}.webp";
            $this->croppedWebpPath = "$targetDir/{$baseName}_cropped.webp";

            imagewebp($this->originalImage, $this->originalWebpPath, 90);

            imagedestroy($this->originalImage);
            unlink($filePath);

            // Reload for cropping if needed
            $this->originalImage = imagecreatefromwebp($this->originalWebpPath);

            return $baseName;
        }

        return "";
    }

    
    public function crop(int $w, int $h): void
    {
        $targetWidth = $w; // 400
        $targetHeight = $h; // 400

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

    public function processImage(UploadedFile|null $file, $entityInstance, string $dir, int $w = 0, int $h = 0): void
    {
        $this->setFile($file);

        if ($file) {
            $filename = $this->upload($dir);
            if($filename){
                if($w > 0 && $h > 0){
                    $this->crop($w,$h);
                }
            }
            $entityInstance->setImage($filename);
        }
    }

    public function persistImage(EntityManagerInterface $entityManager,string $class,$image,string $path): void
    {
        $fqcn = 'App\Entity\\' . $class;
        if (!class_exists($fqcn)) {
            throw new \RuntimeException("Class $fqcn does not exist");
        }

        $entity = new $fqcn();
        $entity->setActive(1);
        $entity->setCreatedAt(new \DateTimeImmutable());
        $entity->setModifiedAt(new \DateTimeImmutable());
        $this->processImage($image, $entity, $path);
        $entityManager->persist($entity);
    }
}