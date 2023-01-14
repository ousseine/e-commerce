<?php

namespace App\Controller\Main;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('main/home.html.twig', [
            'products' => $productRepository->findAllDesc()
        ]);
    }

    #[Route('/product/{slug}', name: 'app_product', methods: ['GET'])]
    public function product(Product $product, Request $request): Response
    {
        $session = $request->getSession();

        $products = $session->get('product', [
            'elements' => []
        ]);

        $productId = $product->getId();
        $products['elements'][$productId] = [
            'product' => $product
        ];

        $session->set('product', $products);

        return $this->render('main/product/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }
}