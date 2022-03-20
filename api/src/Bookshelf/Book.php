<?php
namespace App\Bookshelf;

use DateTime;
use Laminas\InputFilter\Factory as InputFilterFactory;
use Laminas\InputFilter\InputFilterInterface;

class Book
{
    protected string $bookId;
    protected string $authorId;
    protected string $title;
    protected ?string $isbn;
    protected ?string $synopsis;
    protected ?string $datePublished;
    protected string $created;
    protected string $updated;

    public function __construct(array $data)
    {
        $data = $this->validate($data);

        $this->bookId = $data['book_id'] ?? null;
        $this->authorId = $data['author_id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->isbn = $data['isbn'] ?? null;
        $this->synopsis = $data['synopsis'] ?? null;
        $this->datePublished = $data['date_published'] ?? null;

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $created = $data['created'] ?? null;
        $updated = $data['updated'] ?? null;
        if (!$created || !strtotime($created)) {
            $created = $now;
        }
        if (!$updated || !strtotime($updated)) {
            $updated = $now;
        }
        $this->created = $created;
        $this->updated = $updated;
    }

    public function getBookId(): string
    {
        return $this->bookId;
    }

    /**
     * @return array{book_id: string, author_id: string, title: string, isbn: string, synopsis: string, date_published: string, created: string, updated: string}
     */
    public function getArrayCopy(): array
    {
        return [
            'book_id' => $this->bookId,
            'author_id' => $this->authorId,
            'title' => $this->title,
            'isbn' => $this->isbn,
            '$this->synopsis' => $this->synopsis,
            'date_published' => $this->datePublished,
            'created' => $this->created,
            'updated' => $this->updated,
        ];
    }

    public function update($data): void
    {
        $data = $this->validate($data, ['title', 'isbn', 'synopsis', 'date_published']);

        $this->title = $data['title'] ?? $this->title;
        $this->isbn = $data['isbn'] ?? $this->isbn;
        $this->synopsis = $data['synopsis'] ?? $this->synopsis;
        $this->datePublished = $data['date_published'] ?? $this->datePublished;
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
            'book_id' => [
                'required' => true,
                'validators' => [
                    ['name' => 'Uuid'],
                ],
            ],
            'author_id' => [
                'required' => true,
                'validators' => [
                    ['name' => 'Uuid'],
                ],
            ],
            'title' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
            ],
            'isbn' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
            ],
            'synopsis' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
            ],
            'date_published' => [
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
