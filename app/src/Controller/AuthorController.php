<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Model\AuthorDto;
use App\Repository\AuthorRepository;
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

    #[Route(path: '', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload (acceptFormat: 'json')] AuthorDto $authorDto,
        EntityManagerInterface                                $entityManager,
        ValidatorInterface                                    $validator
    ): JsonResponse {
        $author = new Author();
        $author->setFirstname($authorDto->firstname)
            ->setMiddlename($authorDto->middlename ?? null)
            ->setLastname($authorDto->lastname);

        $errors = $validator->validate($author);
        if (\count($errors) > 0) {
            return $this->json((string)$errors, 400);
        }

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->json($author->asArray());
    }

    #[Route(path: '', name: 'all', methods: ['GET'])]
    public function all(
        AuthorRepository                                                                                        $repository,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT, options: ['min_range' => 1, 'max_range' => 100])] int $limit = self::PAGINATION_LIMIT_DEFAULT,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT, options: ['min_range' => 0])] int                     $offset = 0,
        #[MapQueryParameter] bool                                                                               $include_books = false,
    ): JsonResponse {
        $paginator = $repository->getPagination($limit, $offset);

        return $this->json(\array_map(fn(Author $a) => $a->asArray($include_books), (array)$paginator->getIterator()));
    }
}
