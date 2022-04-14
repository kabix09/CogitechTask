<?php

namespace App\Serializer\Denormalizer;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class PostDenormalizer implements ContextAwareDenormalizerInterface, CacheableSupportsMethodInterface
{
    private const POST_SCHEMA = ['id', 'userId', 'title', 'body'];

    private $normalizer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PostDenormalizer constructor.
     * @param ObjectNormalizer $normalizer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ObjectNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        //$user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['userId']]);

        /** @var int $id */
        $id = $data['userId'];

        /** @var User[] $user */
        $user = array_values(array_filter($context['users'], static function(User $user) use ($id) { return $user->getId() === $id;}));

        $post = new Post();
        $post->setId($id);
        $post->setTitle($data['title']);
        $post->setBody($data['body']);
        $post->setAuthor($user[0]);

        return $post;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        if($type !== Post::class)
            return false;

        $dataSchema = array_filter(self::POST_SCHEMA, static function($key, $value) use ($data) { return array_key_exists($key, $data)? true: false; }, ARRAY_FILTER_USE_BOTH);

        return count($dataSchema) === count(self::POST_SCHEMA);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}
