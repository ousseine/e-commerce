<?php

namespace App\Controller\Main;

use App\Form\CheckoutFormType;
use App\Repository\UserRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(CartService $service, Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $cart = $service->get();

        $form = $this->createForm(CheckoutFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_checkout');
        }

        return $this->render('main/checkout/index.html.twig', [
            'controller_name' => 'CheckoutController',
            'cart' => $cart,
            'form' => $form
        ]);
    }
}
