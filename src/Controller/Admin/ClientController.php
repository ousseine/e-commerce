<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_admin_client_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/client/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/{id}', name: 'app_admin_client_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/client/show.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/{id}', name: 'app_admin_client_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_admin_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
