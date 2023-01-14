<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
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
        return $this->getSession()->get('cart', [
            'elements' => [],
            'total' => 0.0
        ]);
    }

    public function add(Product $product): void
    {
        $cart = $this->get();
        $productId = $product->getId();

        if (!isset($cart['elements'][$productId])) {
            $cart['elements'][$productId] = [
                'product' => $product,
                'quantity' => 0
            ];
        }

        $cart['total'] = $cart['total'] + $product->getPrice();
        $cart['elements'][$productId]['quantity'] = $cart['elements'][$productId]['quantity'] + 1;

        $this->getSession()->set('cart', $cart);
    }

    public function remove(Product $product): void
    {
        $cart = $this->get();
        $productId = $product->getId();

        if(!isset($cart['elements'][$productId])) return;

        $cart['total'] = $cart['total'] - $product->getPrice();
        $cart['elements'][$productId]['quantity'] = $cart['elements'][$productId]['quantity'] - 1;

        if($cart['elements'][$productId]['quantity'] <= 0) unset($cart['elements'][$productId]);

        $this->getSession()->set('cart', $cart);
    }

    public function clear(): void
    {
        $this->getSession()->remove('cart');
    }
}