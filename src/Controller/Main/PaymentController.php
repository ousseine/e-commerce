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
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
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
        // préparer les requêtes du paiement
        $paymentRequest = $this->requestRepository->findOneBy(['stripeSessionId' => $stripeSessionId]);
        $paymentRequest
            ->setPaidAt( new DateTimeImmutable())
            ->setValidated(true);
        $this->requestRepository->save($paymentRequest, true);

        // préparer la commande
        $order = new Order();
        $order
            ->setCreatedAt(new DateTimeImmutable())
            ->setReference('SH-'. rand(1000000, 99999999))
            ->setUser($this->getUser())
            ->setPaymentRequest($paymentRequest);
        $this->orderRepository->save($order, true);

        // préparer les quantités commandés
        $cart = $this->cartService->get();
        foreach ($cart['elements'] as $productId => $element) {
            $product = $this->productRepository->find($productId);

            $orderQuantity = new OrderQuantity();
            $orderQuantity
                ->setQuantity($element['quantity'])
                ->setProduct($product)
                ->setFromOrder($order);
            $this->quantityRepository->save($orderQuantity, true);
        }

        // vider le panier
        $this->cartService->clear();

        // envoyer un email de confirmation
        $email = (new Email())
            ->from('contact@admin.com')
            ->to($this->getUser()->getEmail())
            ->subject('Confirmation de votre commande')
            ->text('Sending emails is fun again!')
            ->html(
                "<dev>
                        <p>Bonjour,<p/>
                        <p>Nous vous remercions pour votre commande. 
                        Nous vous tiendrons informé par e-mail lorsque les articles de votre commande auront été expédiés. 
                        Votre date de livraison estimée est indiquée ci-dessous. 
                        Vous pouvez suivre l’état de votre commande ou modifier celle-ci dans Vos commandes sur Amazon.fr.<p/>
                    </dev>"
            );
        $mailer = Transport::fromDsn($_ENV['MAILER_DSN']);
        $mailer->send($email);

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
