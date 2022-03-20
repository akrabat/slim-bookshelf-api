<?php
namespace App\Bookshelf;

use App\BaseURL;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AuthorTransformer
{
    public function __construct(protected BaseURL $baseUrl)
    {
    }

    public function transformCollection(array $authors): array
    {
        $data = [];
        foreach ($authors as $author) {
            $data[] = $this->transform($author);
        }

        return $data;
    }

    public function transform(Author $author): array
    {
        $authorData = $author->getArrayCopy();
        $authorId = $authorData['author_id'];
        unset($authorData['author_id']);

        return [
            'type'=> 'author',
            'id'=> $authorId,
            'attributes'=> $authorData,
            'relationships' => [
                'books' => [
                    'links' => [
                        'self' => (string)$this->baseUrl . '/authors/' . $authorId . '/books',
                    ]
                ]
            ],
        ];
    }
}
