<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class AuthorDto
{
    public function __construct(
        public ?int $id,

        #[Assert\NotBlank]
        public string $firstname,

        public ?string $middlename,

        #[Assert\NotBlank]
        #[Assert\Length (min: 3)]
        public string $lastname,
    ) {
    }
}
