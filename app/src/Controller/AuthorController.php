<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Model\AuthorDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: "/authors", name: "authors_", format: 'json')]
class AuthorController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator,
    ) {
    }

    #[Route(path: '', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload (acceptFormat: 'json')] AuthorDto $authorDto): JsonResponse
    {
        $author = new Author();
        $author->setFirstname($authorDto->firstname)
            ->setMiddlename($authorDto->middlename ?? null)
            ->setLastname($authorDto->lastname);

        $errors = $this->validator->validate($author);
        if (\count($errors) > 0) {
            return $this->json((string)$errors, 400);
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $this->json([
            'id' => $author->getId(),
            'firstname' => $author->getFirstname(),
            'middlename' => $author->getMiddlename(),
            'lastname' => $author->getLastname(),
        ]);
    }
}
