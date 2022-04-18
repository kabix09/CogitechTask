<?php
declare(strict_types=1);

namespace App\Serializer\Denormalizer;

use App\Entity\Post;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class PostsCollectionDenormalizer implements ContextAwareDenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @var PostDenormalizer
     */
    private $postDenormalizer;

    /**
     * PostsCollectionDenormalizer constructor.
     * @param PostDenormalizer $postDenormalizer
     */
    public function __construct(PostDenormalizer $postDenormalizer)
    {
        $this->postDenormalizer = $postDenormalizer;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $postsCollection = [];

        foreach ($data as $post)
        {
            if(array_key_exists('progress', $context))
                $context['progress']->progressAdvance();

            $postsCollection[] = $this->postDenormalizer->denormalize($post, Post::class, 'json', ['users' => $context['users']]);
        }

        return $postsCollection;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return is_array($data) && $type === 'App\Entity\Post[]';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}