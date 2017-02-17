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

        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $mockData = ['author_id' => '2CB0681F-CCBE-417E-ADAD-19E9215EC58C',
                    'name' => 'b',
                    'biography' => 'c',
                    'date_of_birth' => '1980-01-02',
                    'created' => $now,
                    'updated' => $now,
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
