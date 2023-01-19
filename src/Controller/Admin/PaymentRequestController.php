<?php

namespace App\Controller\Admin;

use App\Entity\PaymentRequest;
use App\Form\PaymentRequestType;
use App\Repository\PaymentRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/payment/request')]
class PaymentRequestController extends AbstractController
{
    #[Route('/', name: 'app_admin_payment_request_index', methods: ['GET'])]
    public function index(PaymentRequestRepository $paymentRequestRepository): Response
    {
        return $this->render('admin/payment_request/index.html.twig', [
            'payment_requests' => $paymentRequestRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_payment_request_delete', methods: ['POST'])]
    public function delete(Request $request, PaymentRequest $paymentRequest, PaymentRequestRepository $paymentRequestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentRequest->getId(), $request->request->get('_token'))) {
            $paymentRequestRepository->remove($paymentRequest, true);
        }

        return $this->redirectToRoute('app_admin_payment_request_index', [], Response::HTTP_SEE_OTHER);
    }
}
