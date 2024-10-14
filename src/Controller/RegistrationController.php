<?php

// src/Controller/RegistrationController.php
namespace App\Controller;

// ...
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()->add(
            'email',
            TextType::class
        )->add(
            'password',
            PasswordType::class
        )->add(
            'register',
            SubmitType::class
        )->setMethod('GET')->getForm();

        $form->handleRequest($request);
        dump($form->getData());

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['password']
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(UserRepository $userrepo, Request $request, AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Security $security): Response
    {

        if ($security->getUser() !== null) {
            return $this->redirectToRoute('app_logout');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createFormBuilder()->add(
            'email',
            TextType::class
        )->add(
            'password',
            PasswordType::class
        )->
        add(
            'login',
            SubmitType::class
        )->setMethod('GET')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userrepo->findOneUserByEmail($data['email']);
            if ($user !== null) {
                if ($passwordHasher->isPasswordValid($user, $data['password'])) {
                    $security->login($user);
                    return $this->redirectToRoute('app_songsearch');
                }
            }
        }


        return $this->render('registration/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        $security->logout();
        return $this->redirectToRoute('app_songsearch');
    }

}