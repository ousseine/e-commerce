<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_admin_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('admin/order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/send/{id}', name: 'app_admin_order_send', methods: ['POST'])]
    public function send(Order $order, Request $request, OrderRepository $orderRepository): RedirectResponse
    {
        if ($this->isCsrfTokenValid('send' . $order->getId(), $request->request->get('_token'))) {
            if(!$order->isIsSend()) {
                $order->setIsSend(true);
                $order->setSendAt(new DateTimeImmutable());
            }
            else {
                $order->setIsSend(false);
            }

            // email de confirmation
            $email = (new Email())
                ->from('contact@admin.com')
                ->to($this->getUser()->getEmail())
                ->subject('Commande expedier')
                ->text('Sending emails is fun again!')
                ->html(
                    "<dev>
                        <p>Bonjour,<p/>
                        <p>Nous vous remercions pour votre confiance. 
                        Votre commande vient d'etre expedier. Il vous sera livré dans les plus brefs delai<p/>
                    </dev>"
                );
            $mailer = Transport::fromDsn($_ENV['MAILER_DSN']);
            $mailer->send($email);

            $orderRepository->save($order, true);
        }

        return $this->redirectToRoute('app_admin_order_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute('app_admin_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
