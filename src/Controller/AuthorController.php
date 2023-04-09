<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    #[Route('/api/v1/authors', name: 'all_authors', methods: ['GET'])]
    public function getAllAuthors(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authors = $authorRepository->findAll();
        $jsonAuthors = $serializer->serialize($authors, 'json', ['groups' => 'getPost']);
        return new JsonResponse($jsonAuthors, Response::HTTP_OK, [], true);
    }

    #[Route('/api/v1/authors/{id}', name: 'author', methods: ['GET'])]
    public function getAuthor(Author $author, SerializerInterface $serializer): JsonResponse
    {
        $authorjson = $serializer->serialize($author, 'json', ['groups' => 'getPost']);
        return new JsonResponse($authorjson, Response::HTTP_OK, [], true);
    }
}
