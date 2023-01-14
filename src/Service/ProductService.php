<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductService
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function get()
    {
        return $this->getSession()->get('consultProduct', [
            'elements' => []
        ]);
    }

    public function add(Product $product): void
    {
        $consultProduct = $this->get();
        $productId = $product->getId();

        $consultProduct['elements'][$productId] = [
            'product' => $product
        ];

        $this->getSession()->set('consultProduct', $consultProduct);
    }
}