<?php

namespace App\Controller\Main;

use App\Form\AddressFormType;
use App\Form\PasswordFormType;
use App\Form\ProfileType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // profile
        $user = $this->getUser();
        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        // address
        $address = $user->getAddress();
        $addressForm = $this->createForm(AddressFormType::class, $address);
        $addressForm->handleRequest($request);

        // password
        $passwordForm = $this->createForm(PasswordFormType::class, $user);
        $passwordForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid() || $addressForm->isSubmitted() && $addressForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profile est modifier avec succès');

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        if($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $passwordForm->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe est modifier avec succès');

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/profile/index.html.twig', [
            'profileForm' => $profileForm,
            'addressForm' => $addressForm,
            'passwordForm' => $passwordForm,
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete', name: 'app_profile_delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request, AddressRepository $addressRepository): Response
    {
        $user = $this->getUser();

        if($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $addressRepository->remove($user->getAddress(), true);

            $this->container->get('security.token_storage')->setToken(null);

            $entityManager->persist($user);
            $entityManager->flush($user);
        }

        $this->addFlash('success', 'Vous venez de supprimer votre profile....');

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
