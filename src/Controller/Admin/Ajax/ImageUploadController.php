<?php

namespace App\Controller\Admin\Ajax;

use App\Form\Admin\ImageUploadType;
use App\Entity\Slide;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Modules\ImageService;
use App\Service\Modules\LangService;
use App\Service\Modules\TranslateService;

class ImageUploadController extends AbstractController
{
    public function __construct(
        private ImageService $imageService,
        private readonly LangService $langService,
        private readonly TranslateService $translateService,
    ) {}
    
    #[Route('/image/upload', name: 'image_upload')]
    public function upload(Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImageUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            // Collect errors for debugging
            $errors = [];
            foreach ($form->getErrors(true, false) as $error) {
                $errors[] = $error->getMessage();
            }
            throw new \RuntimeException('Form is invalid: ' . implode('; ', $errors));
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();
            $class = $form->get('class')->getData();
            $path = $form->get('path')->getData();

            if (empty($images)) {
                throw new \RuntimeException('No images uploaded or data retrieved is empty');
            }

            if (empty($class)) {
                throw new \RuntimeException('No class found');
            }

            if (empty($path)) {
                throw new \RuntimeException('No path found');
            }

            try {
                foreach ($images as $image) {
                    $this->imageService->persistImage($entityManager,$class,$image,$path);
                }
                $entityManager->flush();
            } catch (\Throwable $ex) {
                throw new \RuntimeException('Error processing images or saving slides: ' . $ex->getMessage(), 0, $ex);
            }

            $this->addFlash('success', $this->translateService->translateWords("success_upload","Slides uploaded successfully"));

            //return $this->redirectToRoute('admin_slide_index');
        }

         // Detect if this is a Turbo Frame request
        if ($request->headers->get('Turbo-Frame') === 'modal-frame') {
            return $this->render('/admin/modules/_image_upload_frame.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Otherwise render full page (fallback)
        return $this->render('/admin/modules/_image_upload_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}