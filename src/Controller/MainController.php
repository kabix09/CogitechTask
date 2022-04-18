<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MainController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * MainController constructor.
     * @param PostRepository $postRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(PostRepository $postRepository, SerializerInterface $serializer)
    {
        $this->postRepository = $postRepository;
        $this->serializer = $serializer;
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
     * @Route("/post/remove/{id}", name="app_post_remove")
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
        return new Response(
            $this->serializer->serialize(
                $this->postRepository->findAll(),
                'json',
                ['groups' => 'post:read']

            ),
            200,
           ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/posts/{id}", name="app_post_api_item")
     */
    public function getPostItem(Post $post): Response
    {
        return new Response(
            $this->serializer->serialize(
                $post,
                'json',
                ['groups' => 'post:read']

            ),
            200,
            ['Content-Type' => 'application/json']
        );
    }

}
