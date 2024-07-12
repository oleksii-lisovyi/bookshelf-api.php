<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Model\BookDto;
use App\Model\BookUpdateDto;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: "/books", name: "books_", format: 'json')]
class BookController extends AbstractController
{
    const PAGINATION_LIMIT_DEFAULT = 5;

    #[Route('', name: 'create', methods: ['POST'])]
    public function index(
        #[MapRequestPayload (acceptFormat: 'json')] BookDto $bookDto,
        EntityManagerInterface                              $entityManager,
        ValidatorInterface                                  $validator,
        AuthorRepository                                    $authorRepository
    ): JsonResponse {
        // TODO: currently a new book is created every time.
        //  Probably book name + publishing date can be a unique constraint.
        $book = new Book();
        $book->setName($bookDto->name)
            ->setShortDescription($bookDto->shortDescription)
            ->setPublishedAt($bookDto->publishedAt);

        $errors = $validator->validate($book);
        if (\count($errors) > 0) {
            return $this->json((string)$errors, 400);
        }

        $entityManager->persist($book);

        foreach ($bookDto->authors as $a) {
            if ($a->id) {
                $author = $authorRepository->find($a->id);

                if (!$author) {
                    return $this->json('Author can not be found by ID ' . $a->id, 400);
                }
            } else {
                $author = new Author();
                $author->setFirstname($a->firstname)
                    ->setMiddlename($a->middlename ?? null)
                    ->setLastname($a->lastname);

                $errors = $validator->validate($author);
                if (\count($errors) > 0) {
                    return $this->json((string)$errors, 400);
                }

                $entityManager->persist($author);
            }

            $book->addAuthor($author);
        }

        $entityManager->flush();

        return $this->json($book->asArray(true));
    }

    #[Route(path: '', name: 'all', methods: ['GET'])]
    public function all(BookRepository $repository, Request $request): JsonResponse
    {
        $limit = \max(0, $request->query->getInt('limit', self::PAGINATION_LIMIT_DEFAULT));
        $offset = \max(0, $request->query->getInt('offset'));
        $includeAuthors = $request->query->getBoolean('include_authors');

        $paginator = $repository->get($limit, $offset);

        return $this->json(\array_map(fn(Book $b) => $b->asArray($includeAuthors), (array)$paginator->getIterator()));
    }

    #[Route(path: '/{id}', name: 'one', methods: ['GET'])]
    public function one(Book $book, Request $request): JsonResponse
    {
        $includeAuthors = $request->query->getBoolean('include_authors');

        return $this->json($book->asArray($includeAuthors));
    }

    #[Route(path: '/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Book                                                      $book,
        #[MapRequestPayload (acceptFormat: 'json')] BookUpdateDto $bookDto,
        EntityManagerInterface                                    $entityManager,
        ValidatorInterface                                        $validator
    ): JsonResponse {
        $book->setName($bookDto->name)
            ->setShortDescription($bookDto->shortDescription)
            ->setPublishedAt($bookDto->publishedAt);

        $errors = $validator->validate($book);
        if (\count($errors) > 0) {
            return $this->json((string)$errors, 400);
        }

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->json($book->asArray(true));
    }
}
