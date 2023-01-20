<?php

namespace App\Controller\Main;

use App\Entity\Order;
use App\Entity\OrderQuantity;
use App\Entity\PaymentRequest;
use App\Repository\OrderQuantityRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRequestRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Service\PaymentService;
use DateTimeImmutable;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('payment')]
class PaymentController extends AbstractController
{
    private PaymentService $paymentService;
    private CartService $cartService;
    private PaymentRequestRepository $requestRepository;
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private OrderQuantityRepository $quantityRepository;

    public function __construct(
        PaymentService $paymentService,
        CartService $cartService,
        PaymentRequestRepository $requestRepository,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        OrderQuantityRepository $quantityRepository
    )
    {
        $this->paymentService = $paymentService;
        $this->cartService = $cartService;
        $this->requestRepository = $requestRepository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->quantityRepository = $quantityRepository;
    }

    /** * @throws ApiErrorException */
    #[Route('/create-checkout-session', name: 'app_checkout_session')]
    public function create(): Response
    {
        $session = $this->paymentService->payment();

        $paymentRequest = new PaymentRequest();
        $paymentRequest->setCreatedAt(new DateTimeImmutable());
        $paymentRequest->setStripeSessionId($session->id);

        $this->requestRepository->save($paymentRequest, true);

        return $this->redirect($session->url, '303');
    }

    #[Route('/success/{stripeSessionId}', name: 'app_payment_success')]
    public function success(string $stripeSessionId): Response
    {
        $cart = $this->cartService->get();

        // préparer les requêtes du paiement
        $paymentRequest = $this->requestRepository->findOneBy([
            'stripeSessionId' => $stripeSessionId
        ]);

        $paymentRequest
            ->setPaidAt( new DateTimeImmutable())
            ->setValidated(true);
        $this->requestRepository->save($paymentRequest, true);


        // préparer la commande
        $products = $cart['elements'];

        $order = new Order();
        $order
            ->setCreatedAt(new DateTimeImmutable())
            ->setReference('SH-'. rand(1000000, 99999999))
            ->setUser($this->getUser())
            ->addProduct($products)
            ->setPaymentRequest($paymentRequest);
        $this->orderRepository->save($order, true);

        // préparer les quantités commandés

        foreach ($cart['elements'] as $productId => $element) {
            $product = $this->productRepository->find($productId);

            $orderQuantity = new OrderQuantity();
            $orderQuantity
                ->setQuantity($element['quantity'])
                ->addProduct($product)
                ->addFromOrder($order);
            $this->quantityRepository->save($orderQuantity, true);
        }

        // vider le panier
        $this->cartService->clear();

        // envoyer un email de confirmation

        return $this->render('main/payment/success.html.twig', [
            'controller_name' => 'PaymentController'
        ]);
    }

    #[Route('/cancel/{stripeSessionId}', name: 'app_payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('main/payment/cancel.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
