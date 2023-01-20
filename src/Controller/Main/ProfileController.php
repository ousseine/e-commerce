<?php

namespace App\Controller\Main;

use App\Form\AddressFormType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        $address = $user->getAddress();
        $addressForm = $this->createForm(AddressFormType::class, $address);
        $addressForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid() || $addressForm->isSubmitted() && $addressForm->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/profile/index.html.twig', [
            'profileForm' => $profileForm,
            'addressForm' => $addressForm,
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        return $this->render('main/profile/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/delete', name: 'app_profile_delete')]
    public function delete(): Response
    {
        return $this->render('main/profile/index.html.twig');
    }
}
