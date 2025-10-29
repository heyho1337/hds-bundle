<?php

namespace App\Controller\Admin\Ajax;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Menu;

class MenuOrderController extends AbstractController
{
    #[Route('/admin/menu/{id}/order', name: 'menu_order', methods: ['PATCH'])]
    public function updateOrder(Request $request, Menu $menu, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['order_num'])) {
            $menu->setOrderNum($data['order_num']);
            $em->flush();
            return new JsonResponse(['status' => 'success']);
        }
        return new JsonResponse(['status' => 'error', 'message' => 'Invalid data'], 400);
    }
}
