<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class PaymentService
{
    private CartService $cartService;
    private mixed $host;
    private string $protocol;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->host = $_SERVER['SERVER_NAME'];
        $this->protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
    }

    /** * @throws ApiErrorException */
    public function payment(): Session
    {
        $cart = $this->cartService->get();
        $items = [];

        Stripe::setApiKey($_ENV['STRIPE_API_SECRET']);
        Stripe::setApiVersion('2022-11-15');

        foreach ($cart['elements'] as $productId => $element ) {
            $items[] = [
                'amount' => $element['product']->getPrice(),
                'quantity' => $element['quantity'],
                'currency' => 'EUR',
                'name' => $element['product']->getTitle()
            ];
        }

        $success_url = $this->protocol . '://' . $this->host . '/payment/success/{CHECKOUT_SESSION_ID}';
        $cancel_url = $this->protocol . '://' . $this->host . '/payment/cancel/{CHECKOUT_SESSION_ID}';

        return Session::create([
            'line_items' => [
                array_map(fn (array $element) => [
                    'quantity' => $element['quantity'],
                    'price_data' => [
                        'currency' => $element['currency'],
                        'product_data' => [
                            'name' => $element['name']
                        ],
                        'unit_amount' => $element['amount'] * 100
                    ]
                ], $items)
            ],
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
        ]);
    }
}