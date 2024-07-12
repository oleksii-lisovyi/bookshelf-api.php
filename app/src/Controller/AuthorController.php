<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Model\AuthorDto;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: "/authors", name: "authors_", format: 'json')]
class AuthorController extends AbstractController
{
    private const PAGINATION_LIMIT_DEFAULT = 5;

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
    public function all(AuthorRepository $repository, Request $request): JsonResponse
    {
        $limit = \max(0, $request->query->getInt('limit', self::PAGINATION_LIMIT_DEFAULT));
        $offset = \max(0, $request->query->getInt('offset'));
        $includeBooks = $request->query->getBoolean('include_books');

        $paginator = $repository->get($limit, $offset);

        return $this->json(\array_map(fn(Author $a) => $a->asArray($includeBooks), (array)$paginator->getIterator()));
    }
}
