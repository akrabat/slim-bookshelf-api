<?php
namespace BookshelfTest\Action;

use Bookshelf\Action\ListAuthorsAction;
use Bookshelf\Author;
use Bookshelf\AuthorMapper;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class ListAuthorsActionTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsJsonBydefault()
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
        $mockAuthors = [
            new Author($mockData),
        ];

        $AuthorMapper = $this->getMockBuilder(AuthorMapper::class)
            ->setMethods(['fetchAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $AuthorMapper->method('fetchAll')->willReturn($mockAuthors);

        $action = new ListAuthorsAction($logger, $renderer, $AuthorMapper);

        $environment = new Environment([
            'REQUEST_URI' => '/Authors'
        ]);

        $response = $action(
            Request::createFromEnvironment($environment),
            new Response()
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertContains('application/hal+json', $response->getHeaderLine('Content-Type'));

        $body = json_decode((string)$response->getBody(), true);
        self::assertArrayHasKey('_links', $body);

        $expectedData = $mockData;
        $expectedData['_links'] = [
            'self' => [
                'href' => '/authors/' . $mockData['author_id'],
            ],
            'books' => [
                'href' => '/authors/' . $mockData['author_id'] . '/books',
            ],
        ];

        self::assertSame($expectedData, $body['_embedded']['author'][0]);
    }
}
