<?php

namespace App\Controller\Admin\Ajax;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{
    #[Route('/admin/order/{id}', name: 'order', methods: ['PATCH'])]
    public function updateOrder(Request $request, Object $entity, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['order_num'])) {
            $entity->setOrderNum($data['order_num']);
            $em->flush();
            return new JsonResponse(['status' => 'success']);
        }
        return new JsonResponse(['status' => 'error', 'message' => 'Invalid data'], 400);
    }
}
