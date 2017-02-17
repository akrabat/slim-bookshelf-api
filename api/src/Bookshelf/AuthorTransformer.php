<?php
namespace Bookshelf;

use Nocarrier\Hal;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AuthorTransformer
{
    public function transformCollection($authors)
    {
        $hal = new Hal('/authors');

        $count = 0;
        foreach ($authors as $author) {
            $count++;
            $hal->addResource('author', $this->transform($author));
        }

        $hal->setData(['count' => $count]);

        return $hal;
    }

    public function transform($author)
    {
        $data = $author->getArrayCopy();

        $resource = new Hal('/authors/' . $data['author_id'], $data);
        $resource->addLink('books', '/authors/' . $data['author_id'] . '/books');

        return $resource;
    }
}
