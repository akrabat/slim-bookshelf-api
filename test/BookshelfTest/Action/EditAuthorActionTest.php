<?php
namespace BookshelfTest\Action;

use Bookshelf\Action\EditAuthorAction;
use Bookshelf\Author;
use Bookshelf\AuthorMapper;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class EditAuthorActionTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthorIsUpdated()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $renderer = new HalRenderer;

        $authorId = '2CB0681F-CCBE-417E-ADAD-19E9215EC58C';
        $mockData = [
            'author_id' => $authorId,
            'name' => 'b',
            'description' => 'c'
        ];
        $mockAuthor = new Author($mockData);

        $authorMapper = $this->getMockBuilder(AuthorMapper::class)
            ->setMethods(['loadById', 'update'])
            ->disableOriginalConstructor()
            ->getMock();
        $authorMapper->expects($this->once())
            ->method('loadById')
            ->with($this->equalTo($authorId))
            ->willReturn($mockAuthor);
        $authorMapper->expects($this->once())
            ->method('update')
            ->with($this->equalTo($mockAuthor))
            ->willReturn($mockAuthor);

        $action = new EditAuthorAction($logger, $renderer, $authorMapper);

        $request = Request::createFromEnvironment(new Environment());
        $request = $request->withAttribute('id', $authorId);
        $request = $request->withParsedBody($mockData);

        $response = $action($request, new Response());

        $this->assertContains('application/hal+json', $response->getHeaderLine('Content-Type'));

        $body = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('_links', $body);
        $this->assertSame('b', $body['name']);
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

        $action = new EditAuthorAction($logger, $renderer, $authorMapper);

        $request = Request::createFromEnvironment(new Environment());
        $request = $request->withAttribute('id', 'unknown-uuid');

        $this->expectException(ProblemException::class);
        $response = $action($request, new Response());
    }
}
