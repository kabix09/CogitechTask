<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/posts", name="app_post_api_list")
     */
    public function getPosts(): Response
    {
        dd('api version');
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
