<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Serializer\Encoder\{DecoderInterface, JsonEncoder, JsonDecode};

class AppFixtures extends Fixture
{
    public function __construct(private readonly DecoderInterface $jsonDecode)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $files = (new Finder())
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'assets')
            ->name('*.json')
            ->files();

        if (!$files->hasResults()) {
            return;
        }

        $filesIter = $files->getIterator();
        $filesIter->rewind();
        $jsonFile = $filesIter->current();

        \assert($jsonFile instanceof SplFileInfo);

        $arr = $this->jsonDecode->decode($jsonFile->getContents(), JsonEncoder::FORMAT, [JsonDecode::ASSOCIATIVE => true]);

        foreach ($arr as $bookData) {
            $b = new Book();
            $b->setName($bookData['title'])
                ->setShortDescription($bookData['shortDescription'] ?? null)
                ->setPublishedAt(\DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\.vO', $bookData['publishedDate']['$date'] ?? '') ?: null);

            foreach ($bookData['authors'] as $authorFullName) {
                if (empty($authorFullName)) continue;

                $parts = \explode(' ', $authorFullName);

                if (\count($parts) < 2) continue;

                $firstname = \array_shift($parts);
                $lastname = \array_pop($parts);
                if (!empty($parts)) {
                    $middlename = \join(' ', $parts);
                }

                $a = new Author();
                $a->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setMiddlename($middlename ?? null);

                $manager->persist($a);

                $b->addAuthor($a);
            }

            $manager->persist($b);
        }

        $manager->flush();
    }
}
