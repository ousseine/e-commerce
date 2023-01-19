<?php

namespace App\Controller\Main;

use App\Entity\PaymentRequest;
use App\Repository\PaymentRequestRepository;
use App\Service\CartService;
use App\Service\PaymentService;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('payment')]
class PaymentController extends AbstractController
{
    private PaymentService $paymentService;
    private CartService $cartService;
    private PaymentRequestRepository $repository;

    public function __construct(PaymentService $paymentService, CartService $cartService, PaymentRequestRepository $repository)
    {
        $this->paymentService = $paymentService;
        $this->cartService = $cartService;
        $this->repository = $repository;
    }

    /** * @throws ApiErrorException */
    #[Route('/create-checkout-session', name: 'app_checkout_session')]
    public function create(): Response
    {
        $session = $this->paymentService->payment();

        $paymentRequest = new PaymentRequest();
        $paymentRequest->setCreatedAt(new \DateTimeImmutable());
        $paymentRequest->setStripeSessionId($session->id);

        $this->repository->save($paymentRequest, true);

        return $this->redirect($session->url, '303');
    }

    #[Route('/success/{stripeSessionId}', name: 'app_payment_success')]
    public function success(string $stripeSessionId): Response
    {
        $this->cartService->clear();

        $paymentRequest = $this->repository->findOneBy([
            'stripeSessionId' => $stripeSessionId
        ]);

        $paymentRequest
            ->setPaidAt( new \DateTimeImmutable())
            ->setValidated(true);
        $this->repository->save($paymentRequest, true);

        // préparer la commande
        // envoyer un email de confirmation

        return $this->render('main/payment/success.html.twig', [
            'controller_name' => 'PaymentController'
        ]);
    }

    #[Route('/cancel/{stripeSessionId}', name: 'app_payment_cancel')]
    public function cancel(string $stripeSessionId): Response
    {
        return $this->render('main/payment/cancel.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
