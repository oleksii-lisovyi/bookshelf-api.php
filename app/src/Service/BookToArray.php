<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{Author, Book};
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * This class shouldn't really exist, because there should be a way to keep the functionality as the `Book` method.
 * But apparently because the `Book` class is an instance of ORM Entity it's created using 
 * reflection `newInstanceWithoutConstructor` method. Therefore, injecting anything into its constructor, e.g. 
 * `UploadHelper` in this case, doesn't do anything.
 */
readonly class BookToArray
{
    public function __construct(private UploaderHelper $uploaderHelper)
    {
    }

    public function execute(Book $book, bool $includeAuthors = false): array
    {
        $result = [
            'id' => $book->getId(),
            'name' => $book->getName(),
            'short_description' => $book->getShortDescription(),
            'image' => $this->uploaderHelper->asset($book),
        ];

        if ($includeAuthors) {
            $result['authors'] = \array_map(fn(Author $a) => $a->asArray(), (array)$book->getAuthors()->getIterator());
        }

        return $result;
    }
}