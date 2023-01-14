<?php

namespace App\Controller\Main;

use App\Entity\Product;
use App\Service\CartService;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    private CartService $service;
    private ProductService $productService;

    public function __construct(CartService $service, ProductService $productService)
    {
        $this->service = $service;
        $this->productService = $productService;
    }

    #[Route('/', name: 'app_cart', methods: ['GET'])]
    public function index(): Response
    {
        $cart = $this->service->get();

        return $this->render('main/cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['GET', 'POST'])]
    public function addCart(Product $product): Response
    {
        $this->service->add($product);

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['GET', 'POST'])]
    public function removeCart(Product $product): Response
    {
        $this->service->remove($product);

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/clear', name: 'app_cart_clear', methods: ['GET', 'POST'])]
    public function clearCart(): Response
    {
        $this->service->clear();

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
}
