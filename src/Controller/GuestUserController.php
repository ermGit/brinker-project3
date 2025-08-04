<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

//#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/user')]
final class GuestUserController extends AbstractController
{
    #[Route(name: 'app_guest_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $guests = $userRepository->createQueryBuilder('u')
            ->where('JSON_CONTAINS(u.roles, :role) = 1')
            ->setParameter('role', json_encode('ROLE_GUEST'))
            ->getQuery()
            ->getResult();

        return $this->render('guest_user/index.html.twig', [
            'users' => $guests,
        ]);
    }

    #[Route('/new', name: 'app_guest_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_GUEST']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_guest_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('guest_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_guest_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('guest_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_guest_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $user->setRoles(['ROLE_GUEST']);
            $entityManager->flush();

            return $this->redirectToRoute('app_guest_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('guest_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_guest_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_guest_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
