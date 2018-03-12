<?php
namespace ErrorTest\Exception;

use Error\Exception\ProblemException;
use Crell\ApiProblem\ApiProblem;

class ProblemExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider constructionDataProvider
     */
    public function testConstruction($problem, $code, $previous)
    {
        $exception = new ProblemException($problem, $code, $previous);

        $this->assertSame($problem, $exception->getProblem());
    }

    public function constructionDataProvider()
    {
        return [
            'all-elements' => [
                new ApiProblem('a', 'b'),
                0,
                null,
            ],
        ];
    }
}
