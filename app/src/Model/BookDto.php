<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class BookDto
{
    public function __construct(
        public ?int    $id,

        #[Assert\NotBlank]
        public string  $name,

        public ?string $short_description,

        #[Assert\Date]
        public ?string $published_at,

        /**
         * @var AuthorDto[]
         */
        public array $authors = []
    )
    {
    }
}
