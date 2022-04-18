<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoadPostsCommand extends Command
{
    private const URL_FILE_PATH = ['users' => '/users', 'posts' => '/posts'];

    protected static $defaultName = 'app:load-posts';
    protected static $defaultDescription = 'Load posts from http://jsonplaceholder.typicode.com/posts';

    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private string $jsonDataSourceLink;

    /**
     * LoadPostsCommand constructor.
     * @param SerializerInterface $serializer
     * @param HttpClientInterface $client
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, HttpClientInterface $client, EntityManagerInterface $entityManager, string $jsonDataSourceLink)
    {
        parent::__construct(self::$defaultName);

        $this->client = $client;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->jsonDataSourceLink = $jsonDataSourceLink;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $responseUsers = $this->client->request('GET', $this->jsonDataSourceLink . self::URL_FILE_PATH['users']);
        $responsePosts = $this->client->request('GET', $this->jsonDataSourceLink . self::URL_FILE_PATH['posts']);

        $users = $this->serializer->deserialize($responseUsers->getContent(), 'App\Entity\User[]', 'json');

        $io = new SymfonyStyle($input, $output);
        $io->progressStart(2*count($responsePosts->toArray()));

        $posts = $this->serializer->deserialize($responsePosts->getContent(), 'App\Entity\Post[]', 'json', ['users' => $users, 'progress' => $io]);

        foreach ($posts as $post)
        {
            $io->progressAdvance();
            $this->entityManager->persist($post);
        }

        $this->entityManager->flush();

        $io->progressFinish();
        $io->success('Posts loaded successfully');

        return Command::SUCCESS;
    }
}
