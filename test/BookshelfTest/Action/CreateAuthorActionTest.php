<?php
namespace BookshelfTest\Action;

use Bookshelf\Action\CreateAuthorAction;
use Bookshelf\Author;
use Bookshelf\AuthorMapper;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class CreateAuthorActionTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsJsonByDefault()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $renderer = new HalRenderer;

        $mockData = ['author_id' => 'a',
                    'name' => 'b',
                    'biography' => 'c',
                    'date_of_birth' => 'd',
                    'created' => 'e',
                    'updated' => 'f',
                    ];
        $mockAuthor = new Author($mockData);

        $authorMapper = $this->getMockBuilder(AuthorMapper::class)
            ->setMethods(['insert'])
            ->disableOriginalConstructor()
            ->getMock();
        $authorMapper->expects($this->once())
            ->method('insert')
            ->with($this->equalTo($mockAuthor))
            ->willReturn(true);

        $action = new CreateAuthorAction($logger, $renderer, $authorMapper);

        $request = Request::createFromEnvironment(new Environment());
        $request = $request->withParsedBody($mockData);
        $response = $action($request, new Response());

        $this->assertSame(201, $response->getStatusCode());
        $this->assertContains('application/hal+json', $response->getHeaderLine('Content-Type'));

        $body = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('_links', $body);
        $this->assertSame('b', $body['name']);
    }
}
