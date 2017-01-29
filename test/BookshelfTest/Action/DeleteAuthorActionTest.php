<?php
namespace BookshelfTest\Action;

use Bookshelf\Action\DeleteAuthorAction;
use Bookshelf\Author;
use Bookshelf\AuthorMapper;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class DeleteAuthorActionTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthorIsDeleted()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $renderer = new HalRenderer;

        $authorId = 'a';

        $authorMapper = $this->getMockBuilder(AuthorMapper::class)
            ->setMethods(['loadById', 'delete'])
            ->disableOriginalConstructor()
            ->getMock();
        $authorMapper->expects($this->once())
            ->method('loadById')
            ->with($this->equalTo($authorId))
            ->willReturn(new Author([
                'author_id' => 'DF63E044-35D6-4E01-919D-65F8F56A7E76',
                'name' => 'Some Name',
                ]));
        $authorMapper->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($authorId))
            ->willReturn(1);

        $action = new DeleteAuthorAction($logger, $renderer, $authorMapper);

        $request = Request::createFromEnvironment(new Environment());
        $request = $request->withAttribute('id', $authorId);

        $response = $action($request, new Response());

        self::assertSame(204, $response->getStatusCode());
    }

    public function testThrowsExceptionOnNotFound()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $renderer = new HalRenderer;

        $authorMapper = $this->getMockBuilder(AuthorMapper::class)
            ->setMethods(['loadById'])
            ->disableOriginalConstructor()
            ->getMock();
        $authorMapper->expects($this->once())
            ->method('loadById')
            ->with($this->equalTo('unknown-uuid'))
            ->willReturn(false);

        $action = new DeleteAuthorAction($logger, $renderer, $authorMapper);

        $request = Request::createFromEnvironment(new Environment());
        $request = $request->withAttribute('id', 'unknown-uuid');

        $this->expectException(ProblemException::class);
        $response = $action($request, new Response());
    }
}
