<?php
namespace BookshelfTest;

use Bookshelf\Author;
use Bookshelf\AuthorTransformer;
use Nocarrier\Hal;

class AuthorTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $data = [
            'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
            'name' => 'b',
            'biography' => 'c',
            'date_of_birth' => '1980-01-02',
            'created' => $now,
            'updated' => $now,

        ];
        $author = new Author($data);

        $transformer = new AuthorTransformer();
        $hal = $transformer->transform($author);

        self::assertInstanceOf(Hal::class, $hal);
        $halData = $hal->getData();
        self::assertSame($data, $halData);
    }

    public function testTransformCollection()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $data = [
            'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
            'name' => 'b',
            'biography' => 'c',
            'date_of_birth' => '1980-01-02',
            'created' => $now,
            'updated' => $now,

        ];
        $author = new Author($data);
        $authors = [$author];

        $transformer = new AuthorTransformer();
        $hal = $transformer->transformCollection($authors);

        self::assertInstanceOf(Hal::class, $hal);

        $halData = $hal->getData();
        self::assertSame(1, $halData['count']);

        $halResources = $hal->getResources();
        self::assertArrayHasKey('author', $halResources);
        self::assertInstanceOf(Hal::class, $halResources['author'][0]);
    }
}
