<?php

namespace App\Controller\Admin;

use App\Entity\OrderQuantity;
use App\Form\OrderQuantityType;
use App\Repository\OrderQuantityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/order-quantity')]
class OrderQuantityController extends AbstractController
{
    #[Route('/', name: 'app_admin_order_quantity_index', methods: ['GET'])]
    public function index(OrderQuantityRepository $orderQuantityRepository): Response
    {
        return $this->render('admin/order_quantity/index.html.twig', [
            'order_quantities' => $orderQuantityRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_order_quantity_show', methods: ['GET'])]
    public function show(OrderQuantity $orderQuantity): Response
    {
        return $this->render('admin/order_quantity/show.html.twig', [
            'order_quantity' => $orderQuantity,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_order_quantity_delete', methods: ['POST'])]
    public function delete(Request $request, OrderQuantity $orderQuantity, OrderQuantityRepository $orderQuantityRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderQuantity->getId(), $request->request->get('_token'))) {
            $orderQuantityRepository->remove($orderQuantity, true);
        }

        return $this->redirectToRoute('app_admin_order_quantity_index', [], Response::HTTP_SEE_OTHER);
    }
}
