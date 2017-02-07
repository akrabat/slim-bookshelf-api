<?php
namespace Bookshelf;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Zend\InputFilter\Factory as InputFilterFactory;

class Author
{
    protected $author_id;
    protected $name;
    protected $biography;
    protected $date_of_birth;
    protected $created;
    protected $updated;

    public function __construct(array $data)
    {
        $data = $this->validate($data);

        $this->author_id = $data['author_id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->biography = $data['biography'] ?? null;
        $this->date_of_birth = $data['date_of_birth'] ?? null;
        $this->created = $data['created'] ?? null;
        $this->updated = $data['updated'] ?? null;


        $now = (new \DateTime())->format('Y-m-d H:i:s');
        if (!strtotime($this->created)) {
            $this->created = $now;
        }
        if (!strtotime($this->updated)) {
            $this->updated = $now;
        }
    }

    public function getArrayCopy()
    {
        return [
            'author_id' => $this->author_id,
            'name' => $this->name,
            'biography' => $this->biography,
            'date_of_birth' => $this->date_of_birth,
            'created' => $this->created,
            'updated' => $this->updated,
        ];
    }

    public function update($data)
    {
        $data = $this->validate($data, ['name', 'biography', 'date_of_birth']);

        $this->name = $data['name'] ?? $this->name;
        $this->biography = $data['biography'] ?? $this->biography;
        $this->date_of_birth = $data['date_of_birth'] ?? $this->date_of_birth;
    }

    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
    public function validate($data, $elements = [])
    {
        $inputFilter = $this->createInputFilter($elements);
        $inputFilter->setData($data);

        if ($inputFilter->isValid()) {
            return $inputFilter->getValues();
        }

        $problem = new ApiProblem(
            'Validation failed',
            'about:blank',
            400
        );
        $problem['errors'] = $inputFilter->getMessages();

        throw new ProblemException($problem);
    }

    protected function createInputFilter($elements = [])
    {
        $specification = [
            'author_id' => [
                'required' => true,
                'validators' => [
                    ['name' => 'Uuid'],
                ],
            ],
            'name' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
            ],
            'biography' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
            ],
            'date_of_birth' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'Date'],
                    [
                        'name' => 'LessThan',
                        'options' => [
                            'max' => date('Y-m-d'),
                            'inclusive' => true,
                        ],
                    ],
                ],
            ],
            'created' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Date',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'format' => 'Y-m-d H:i:s',
                        ],
                    ],
                    [
                        'name' => 'LessThan',
                        'options' => [
                            'max' => date('Y-m-d H:i:s'),
                            'inclusive' => true,
                        ],
                    ],
                ],
            ],
            'updated' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Date',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'format' => 'Y-m-d H:i:s',
                        ],
                    ],
                    [
                        'name' => 'LessThan',
                        'options' => [
                            'max' => date('Y-m-d H:i:s'),
                            'inclusive' => true,
                        ],
                    ],
                ],
            ],
        ];

        if ($elements) {
            $specification = array_filter(
                $specification,
                function ($key) use ($elements) {
                    return in_array($key, $elements);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        $factory = new InputFilterFactory();
        $inputFilter = $factory->createInputFilter($specification);

        return $inputFilter;
    }
}
