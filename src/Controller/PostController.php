<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\AuthorRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PostController extends AbstractController
{
    #[Route('/api/v1/posts', name: 'allPost', methods: ['GET'])]
    public function getAllPosts(PostRepository $postRepository, SerializerInterface $serializer): JsonResponse
    {
        $posts = $postRepository->findAll();
        $jsonPosts = $serializer->serialize($posts, 'json', ['groups' => 'getPost']);

        return new JsonResponse($jsonPosts, Response::HTTP_OK, [], true);
    }

    #[Route('/api/v1/posts/{id}', name: 'detailPost', methods: ['GET'])]
    public function getPost(Post $post, SerializerInterface $serializer): JsonResponse
    {
        $jsonPost = $serializer->serialize($post, 'json', ['groups' => 'getPost']);
        return new JsonResponse($jsonPost, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/v1/posts/{id}', name: 'deletePost', methods: ['DELETE'])]
    public function deletePost(Post $post, PostRepository $postRepository): JsonResponse
    {
        $postRepository->remove($post, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/v1/posts', name: 'createPost', methods: ['POST'])]
    public function createPost(
        Request $request,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
        AuthorRepository $authorRepository
    ): JsonResponse {

        $post = $serializer->deserialize($request->getContent(), Post::class, 'json');
        $content = $request->toArray();
        $authorid = $content['authorId'] ?? -1;

        $author = $authorRepository->find($authorid);
        $post->setAuthor($author);
        $entityManager->persist($post);
        $entityManager->flush();

        $post_json = $serializer->serialize($post, 'json', ['groups' => 'getPost']);
        $location = $urlGenerator->generate('detailPost', ['id' => $post->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($post_json, Response::HTTP_CREATED, ['location' => $location], true);
    }

    #[Route('/api/v1/posts/{id}', name: 'updatePost', methods: ['PUT'])]
    public function updatePost(
        Request $request,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
        AuthorRepository $authorRepository,
        Post $currentPost
    ): JsonResponse {

        //Donc ici on veut désérialiser directement à l'intérieur de $currentpost
        $updatedPost = $serializer->deserialize(
            $request->getContent(),
            Post::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentPost]
        );
        $content = $request->toArray();
        $authorid = $content['authorId'] ?? -1;

        $author = $authorRepository->find($authorid);
        $updatedPost->setAuthor($author);
        $entityManager->persist($updatedPost);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
