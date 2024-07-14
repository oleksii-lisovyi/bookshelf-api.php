<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\{Author, Book};
use App\Model\AuthorDto;
use App\Repository\{AuthorRepository, BookRepository};
use App\Service\EntityToArray;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\{MapQueryParameter, MapRequestPayload};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: "/authors", name: "authors_", format: 'json')]
class AuthorController extends AbstractController
{
    private const PAGINATION_LIMIT_DEFAULT = 10;

    public function __construct(private readonly EntityToArray $entityToArray)
    {
    }

    #[Route(path: '', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload (acceptFormat: 'json')] AuthorDto $authorDto,
        EntityManagerInterface                                $entityManager,
        ValidatorInterface                                    $validator,
        BookRepository                                        $bookRepository
    ): JsonResponse
    {
        $author = Author::fromDto($authorDto);

        $errors = $validator->validate($author);
        if (\count($errors) > 0) {
            return $this->json((string)$errors, 400);
        }

        $entityManager->persist($author);

        foreach ($authorDto->books as $bookDto) {
            if ($bookDto->id) {
                $book = $bookRepository->find($bookDto->id);

                if (!$book) {
                    return $this->json('Book can not be found by ID ' . $bookDto->id, 400);
                }
            } else {
                $book = Book::fromDto($bookDto);

                $errors = $validator->validate($book);
                if (\count($errors) > 0) {
                    return $this->json((string)$errors, 400);
                }

                $entityManager->persist($book);
            }

            $author->addBook($book);
        }

        $entityManager->flush();

        return $this->json($this->entityToArray->authorToArray($author, true));
    }

    #[Route(path: '', name: 'all', methods: ['GET'])]
    public function all(
        AuthorRepository                                                                                        $repository,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT, options: ['min_range' => 1, 'max_range' => 100])] int $limit = self::PAGINATION_LIMIT_DEFAULT,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT, options: ['min_range' => 0])] int                     $offset = 0,
        #[MapQueryParameter(name: 'include_books')] bool                                                        $includeBooks = false,
    ): JsonResponse
    {
        $paginator = $repository->getPagination($limit, $offset);

        return $this->json(\array_map(fn(Author $a) => $this->entityToArray->authorToArray($a, $includeBooks), (array)$paginator->getIterator()));
    }
}
