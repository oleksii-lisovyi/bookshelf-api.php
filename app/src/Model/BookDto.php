<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class BookDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        public ?string $shortDescription,

        public ?string $image,

        #[Assert\Date]
        public ?\DateTimeInterface $publishedAt,

        /**
         * @var AuthorDto[]
         */
        #[Assert\NotBlank]
        public array $authors
    ) {
    }
}
