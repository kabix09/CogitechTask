<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse|Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if($this->isGranted("ROLE_USER"))
        {
            return $this->redirectToRoute('app_post_list');
        }

        $form = $this->createForm(LoginType::class, new Manager());
        $form->handleRequest($request);

        return $this->render('form/login.html.twig', [
            'loginForm' => $form->createView(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse|Response
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        AuthenticationUtils $authenticationUtils,
        UserAuthenticatorInterface  $userAuthenticator,
        LoginFormAuthenticator $loginFormAuthenticator,
        EntityManagerInterface $entityManager)
    {
        if($this->isGranted("ROLE_USER"))
        {
            return $this->redirectToRoute('app_post_list');
        }

        $form = $this->createForm(RegisterType::class, new Manager());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Manager $user */
            $user = $form->getData();

            $user->setPassword(
                $passwordHasher->hashPassword($user, $user->getPassword())
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser($user, $loginFormAuthenticator, $request);
        }

        return $this->render('form/register.html.twig', [
            'registerForm' => $form->createView(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout() { }
}
