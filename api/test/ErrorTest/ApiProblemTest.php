<?php
namespace ErrorTest;

use Error\ApiProblem;

class ApiProblemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testConstruction($title, $type, $status, $detail)
    {
        $problem = new ApiProblem($title, $type, $status, $detail);

        $expected = [
            'title' => $title,
            'type' => $type,
            'status' => $status,
            'detail' => $detail
        ];

        $this->assertEquals($expected, $problem->asArray());
    }

    public function dataProvider()
    {
        return [
            'all-elements' => [
                'a',
                'b',
                501,
                'c'
            ],
        ];
    }
}
