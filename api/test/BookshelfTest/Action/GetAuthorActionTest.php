<?php
namespace BookshelfTest\Action;

use Bookshelf\Action\GetAuthorAction;
use Bookshelf\Author;
use Bookshelf\AuthorMapper;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class GetAuthorActionTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsJsonByDefault()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $renderer = new HalRenderer;

        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $mockData = new Author(['author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'description' => 'c',
                    'created' => $now,
                    'updated' => $now,]);

        $authorMapper = $this->getMockBuilder(AuthorMapper::class)
            ->setMethods(['loadById'])
            ->disableOriginalConstructor()
            ->getMock();
        $authorMapper->expects($this->once())
            ->method('loadById')
            ->with($this->equalTo('4BA473C8-DEBE-4441-9001-A21617BD4515'))
            ->willReturn($mockData);

        $action = new GetAuthorAction($logger, $renderer, $authorMapper);

        $request = Request::createFromEnvironment(new Environment());
        $request = $request->withAttribute('id', '4BA473C8-DEBE-4441-9001-A21617BD4515');

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
            ->with($this->equalTo('4BA473C8-DEBE-4441-9001-A21617BD4515'))
            ->willReturn(false);

        $action = new GetAuthorAction($logger, $renderer, $authorMapper);

        $request = Request::createFromEnvironment(new Environment());
        $request = $request->withAttribute('id', '4BA473C8-DEBE-4441-9001-A21617BD4515');

        $this->expectException(ProblemException::class);
        $response = $action($request, new Response());
    }
}
