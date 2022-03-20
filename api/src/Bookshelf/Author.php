<?php
namespace App\Bookshelf;

use DateTime;
use Laminas\InputFilter\Factory as InputFilterFactory;
use Laminas\InputFilter\InputFilterInterface;

class Author
{
    protected string $authorId;
    protected string $name;
    protected ?string $biography;
    protected ?string $dateOfBirth;
    protected string $created;
    protected string $updated;

    public function __construct(array $data)
    {
        $data = $this->validate($data);

        $this->authorId = $data['author_id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->biography = $data['biography'] ?? null;
        $this->dateOfBirth = $data['date_of_birth'] ?? null;

        $created = $data['created'] ?? null;
        $updated = $data['updated'] ?? null;
        $now = (new DateTime())->format('Y-m-d H:i:s');
        if (!$created || !strtotime($created)) {
            $created = $now;
        }
        if (!$updated || !strtotime($updated)) {
            $updated = $now;
        }
        $this->created = $created;
        $this->updated = $updated;
    }

    /**
     * @return array{author_id: string, name: string, biography: string, date_of_birth: string, created: string, updated: string}
     */
    public function getArrayCopy(): array
    {
        return [
            'author_id' => $this->authorId,
            'name' => $this->name,
            'biography' => $this->biography,
            'date_of_birth' => $this->dateOfBirth,
            'created' => $this->created,
            'updated' => $this->updated,
        ];
    }

    public function update($data): void
    {
        $data = $this->validate($data, ['name', 'biography', 'date_of_birth']);

        $this->name = $data['name'] ?? $this->name;
        $this->biography = $data['biography'] ?? $this->biography;
        $this->dateOfBirth = $data['date_of_birth'] ?? $this->dateOfBirth;
    }

    /**
     * Validate data to be applied to this entity
     */
    public function validate(array $data, array $elements = []): array
    {
        $inputFilter = $this->createInputFilter($elements);
        $inputFilter->setData($data);

        if ($inputFilter->isValid()) {
            return $inputFilter->getValues();
        }

        throw new ValidationException('Validation failed', $inputFilter->getMessages());
    }

    protected function createInputFilter($elements = []): InputFilterInterface
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
                static function ($key) use ($elements) {
                    return in_array($key, $elements, true);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return (new InputFilterFactory())->createInputFilter($specification);
    }
}
