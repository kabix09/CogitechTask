<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Entity\Post;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Repository\PostRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class MainController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * MainController constructor.
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/", name="app_main")
     */
    public function index(): Response
    {
        $posts = $this->postRepository->findAll();

        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/lista", name="app_post_list")
     */
    public function getLists(): Response
    {
        $posts = $this->postRepository->findAll();

        return $this->render('main/list.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @IsGranted("ROLE_POST_MANAGER")
     * @Route("/post/{id}", name="app_post_remove")
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function removePost(Post $post, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_post_list');
    }

    /**
     * @Route("/posts", name="app_post_api_list")
     */
    public function getPosts(): Response
    {
        dd('api version');
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

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
