<?php
namespace BookshelfTest;

use Bookshelf\Author;
use Bookshelf\AuthorMapper;
use Monolog\Logger;

class AuthorMapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \AppTest\Bootstrap::seedDatabase('truncate-all.sql');
        \AppTest\Bootstrap::seedDatabase('author.sql');
    }

    private function getMockLogger()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        return $logger;
    }

    public function testFetchAll()
    {
        $logger = $this->getMockLogger();

        $container = \AppTest\Bootstrap::getContainer();
        $db = $container->get('db');

        $mapper = new AuthorMapper($logger, $db);
        $data = $mapper->fetchAll();

        $expected = new Author([
            'author_id' => 'f075512f-9734-304c-b839-b86174143c07',
            'name' => 'Ann McCaffrey',
            'biography' => "Anne Inez McCaffrey was an American-born Irish writer,"
                . " best known for the Dragonriders of Pern fantasy series. Early in"
                . " McCaffrey's 46-year career as a writer, she became the first"
                . " woman to win a Hugo Award for fiction and the first to win a"
                . " Nebula Award.",
            'date_of_birth' => '1926-04-01',
            'created' => '2017-01-28 22:00:00',
            'updated' => '2017-01-28 22:00:00',
        ]);
        self::assertEquals(2, count($data));
        self::assertEquals($expected, $data[0]);
    }

    public function testLoadById()
    {
        $logger = $this->getMockLogger();

        $container = \AppTest\Bootstrap::getContainer();
        $db = $container->get('db');

        $mapper = new AuthorMapper($logger, $db);
        $author = $mapper->loadById('77707f1b-400c-3fe0-b656-c0b14499a71d');

        $expected = new Author([
            'author_id' => '77707f1b-400c-3fe0-b656-c0b14499a71d',
            'name' => 'Suzanne Collins',
            'biography' => 'Suzanne Marie Collins is an American television writer and novelist,'
                .' best known as the author of The Underland Chronicles and The Hunger Games trilogy.',
            'date_of_birth' => '1962-08-10',
            'created' => '2017-01-28 22:00:00',
            'updated' => '2017-01-28 22:00:00',
        ]);
        self::assertEquals($expected, $author);
    }

    public function testLoadByIdReturnsFalseOnFailure()
    {
        $logger = $this->getMockLogger();

        $container = \AppTest\Bootstrap::getContainer();
        $db = $container->get('db');

        $mapper = new AuthorMapper($logger, $db);
        $author = $mapper->loadById('not-here');

        self::assertEquals(false, $author);
    }

    public function testInsert()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $container = \AppTest\Bootstrap::getContainer();
        $db = $container->get('db');

        $author = new Author([
            'author_id' => 'D28677E1-CFCA-4F8E-B9E0-6184F2DE736F',
            'name' => 'Foo',
            'biography' => null,
            'date_of_birth' => null,
            'created' => '2017-01-28 22:00:01',
            'updated' => '2017-01-28 22:00:01',
        ]);

        $mapper = new AuthorMapper($logger, $db);
        $result = $mapper->insert($author);

        self::assertInstanceOf(Author::class, $result);

        // check that the updated property is more recent
        $newData = $result->getArrayCopy();
        self::assertGreaterThan('2017-01-28 22:00:01', $newData['updated']);
    }

    public function testUpdate()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $container = \AppTest\Bootstrap::getContainer();
        $db = $container->get('db');

        $mapper = new AuthorMapper($logger, $db);
        $author = $mapper->loadById('77707f1b-400c-3fe0-b656-c0b14499a71d');

        $author->update(['name' => 'Someone Else']);
        $result = $mapper->update($author);

        self::assertInstanceOf(Author::class, $result);

        // Reload the author from the database and check that it is updated
        $newAuthor = $mapper->loadById('77707f1b-400c-3fe0-b656-c0b14499a71d');
        $newData = $newAuthor->getArrayCopy();
        self::assertSame('Someone Else', $newData['name']);
        self::assertGreaterThanOrEqual(date('Y-m-d H:i:s'), $newData['updated']);
    }

    public function testDelete()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $container = \AppTest\Bootstrap::getContainer();
        $db = $container->get('db');

        $mapper = new AuthorMapper($logger, $db);

        // ensure record exists
        $author = $mapper->loadById('77707f1b-400c-3fe0-b656-c0b14499a71d');

        $result = $mapper->delete('77707f1b-400c-3fe0-b656-c0b14499a71d');

        self::assertTrue($result);

        // Reload the author from the database and ensure it fails
        $author = $mapper->loadById('77707f1b-400c-3fe0-b656-c0b14499a71d');
        self::assertFalse($author);
    }

    public function testDeleteOfNoRecord()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $container = \AppTest\Bootstrap::getContainer();
        $db = $container->get('db');

        $mapper = new AuthorMapper($logger, $db);
        $result = $mapper->delete('unknown-uuid');
        self::assertFalse($result);
    }
}
