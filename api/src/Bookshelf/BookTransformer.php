<?php
namespace App\Bookshelf;

use App\BaseURL;

/**
 * Transform an Book (or collection of Books) into Hal resource
 */
class BookTransformer
{
    public function __construct(protected BaseURL $baseUrl)
    {
    }

    public function transformCollection(array $books): array
    {
        $data = [];
        foreach ($books as $book) {
            $data[] = $this->transform($book);
        }

        return $data;
    }

    public function transform(Book $book): array
    {
        $bookData = $book->getArrayCopy();
        $bookId = $bookData['book_id'];
        unset($bookData['book_id']);

        return [
            'type'=> 'book',
            'id'=> $bookId,
            'attributes'=> $bookData,
            'relationships' => [
                'author' => [
                    'links' => [
                        'self' => (string)$this->baseUrl . '/authors/' . $bookData['author_id'],
                    ]
                ]
            ],
        ];
    }
}
