<?php
namespace BookshelfTest;

use Bookshelf\Author;

class AuthorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testConstruction($data)
    {
        $entity = new Author($data);
        $newData = $entity->getArrayCopy();

        // check dates separately
        unset($data['created']);
        unset($data['updated']);
        unset($newData['created']);
        unset($newData['updated']);

        self::assertEquals($data, $newData);
    }

    public function dataProvider()
    {
        return [
            'all-elements' => [
                [
                    'author_id' => 'a',
                    'name' => 'b',
                    'biography' => 'c',
                    'date_of_birth' => 'd',
                ]
            ],
            'all-null' => [
                [
                    'author_id' => null,
                    'name' => null,
                    'biography' => null,
                    'date_of_birth' => null,
                ]
            ],
        ];
    }

    public function testDatesAreSet()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $entity = new Author([
            'created' => $now,
            'updated' => $now,
        ]);
        $array = $entity->getArrayCopy();

        self::assertSame($now, $array['created']);
        self::assertSame($now, $array['updated']);
    }

    public function testDatesAreSetIfNull()
    {
        $entity = new Author([]);
        $array = $entity->getArrayCopy();

        self::assertNotNull($array['created']);
        self::assertNotNull($array['updated']);
    }
}
