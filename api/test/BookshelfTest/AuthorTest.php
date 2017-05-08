<?php
namespace BookshelfTest;

use Bookshelf\Author;

class AuthorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider constructorProvider
     */
    public function testConstruction($inputData, $expectedData)
    {
        if (is_string($expectedData)) {
            $this->setExpectedException($expectedData);
        }
        $entity = new Author($inputData);
        $newData = $entity->getArrayCopy();

        if (is_array($expectedData)) {
            unset($newData['created']);
            unset($newData['updated']);
            self::assertEquals($expectedData, $newData);
        }
    }

    public function constructorProvider()
    {
        return [
            'all-elements' => [
                [
                    'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'biography' => 'c',
                    'date_of_birth' => '1980-01-02',
                ],
                [
                    'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'biography' => 'c',
                    'date_of_birth' => '1980-01-02',
                ],
            ],
            'allowed-nulls' => [
                [
                    'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'biography' => null,
                    'date_of_birth' => null,
                ],
                [
                    'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'biography' => null,
                    'date_of_birth' => null,
                ],
            ],
            'string-trim' => [
                [
                    'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => ' b ',
                    'biography' => "\tc ",
                    'date_of_birth' => " 1980-01-02\n",
                ],
                [
                    'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'biography' => 'c',
                    'date_of_birth' => '1980-01-02',
                ],
            ],
            'date-of-bith-past' => [
                [
                    'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'biography' => 'c',
                    'date_of_birth' => date('Y-m-d', strtotime('+1 day')),
                ],
                '\Error\Exception\ProblemException',
            ],
        ];
    }

    public function testDatesAreSet()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $entity = new Author([
            'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
            'name' => 'b',
            'created' => $now,
            'updated' => $now,
        ]);
        $array = $entity->getArrayCopy();

        self::assertSame($now, $array['created']);
        self::assertSame($now, $array['updated']);
    }

    public function testDatesAreSetIfNull()
    {
        $entity = new Author([
            'author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
            'name' => 'b',
        ]);
        $array = $entity->getArrayCopy();

        self::assertNotNull($array['created']);
        self::assertNotNull($array['updated']);
    }
}
