<?php

namespace App;

use App\Bookshelf\Author;
use App\Bookshelf\AuthorMapper;
use App\Bookshelf\Book;
use App\Bookshelf\BookMapper;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildSchema;
use Psr\Log\LoggerInterface;

class GraphQLSchemaBuilder
{
    protected array $resolvers = [];

    public function __construct(
        protected LoggerInterface $logger,
        protected AuthorMapper $authorMapper,
        protected BookMapper $bookMapper,
    ) {
        $this->resolvers = $this->buildResolvers();
    }

    public function build(): Schema
    {
        $contents = file_get_contents(__DIR__ . '/../schema/schema.graphqls');
        $schema = BuildSchema::build($contents);

        // set up default field resolver to use our resolvers
        Executor::setDefaultFieldResolver([$this, 'fieldResolver']);

        return $schema;
    }

    /*
     * Resolvers are functions that read the database.
     *
     * We set one up for each object in our schema
     */
    public function buildResolvers(): array
    {
        return [
            'Query' => [
                'node' => function ($root, $args, $context) {
                    [$v, $type, $id] = explode('|', base64_decode($args['id']));
                    switch ($type) {
                        case 'Author':
                            $data = $this->authorMapper->loadById($id)->getArrayCopy();
                            $data['dateOfBirth'] = $data['date_of_birth'];
                            unset($data['date_of_birth'], $data['author_id']);
                            break;
                        case 'Book':
                            $data = $this->bookMapper->loadById($id)->getArrayCopy();
                            $data['datePublished'] = $data['date_published'];
                            unset($data['date_pubished'], $data['book_id']);
                            break;
                    };
                    $data['id'] = $args['id'];
                    $data['__typename'] = $type;
                    return $data;
                },
                'author' => function ($root, $args, $context) {
                    $name = $args['name'];
                    $data = $this->authorMapper->loadByName($name)->getArrayCopy();
                    $data['dateOfBirth'] = $data['date_of_birth'];
                    $data['id'] = base64_encode('1|Author|' . $data['author_id']);
                    $data['__typename'] = 'Author';
                    unset($data['date_of_birth'], $data['author_id']);
                    return $data;
                },
                'authors' => function ($root, $args, $context) {
                    return array_map(
                        static function (Author $author) {
                            $data = $author->getArrayCopy();
                            $data['id'] = base64_encode('1|Author|' . $data['author_id']);
                            $data['__typename'] = 'Author';
                            return $data;
                        },
                        $this->authorMapper->fetchAll()
                    );
                },
                'book' => function ($root, $args, $context) {
                    $name = $args['title'];
                    $data = $this->bookMapper->loadByTitle($name)->getArrayCopy();
                    $data['datePublished'] = $data['date_published'];
                    $data['id'] = base64_encode('1|Book|' . $data['book_id']);
                    $data['__typename'] = 'Book';
                    return $data;
                },
                'books' => function ($root, $args, $context) {
                    return array_map(
                        static function (Book $book) {
                            $data = $book->getArrayCopy();
                            $data['datePublished'] = $data['date_published'];
                            $data['id'] = base64_encode('1|Book|' . $data['author_id']);
                            $data['__typename'] = 'Book';
                            unset($data['date_published'], $data['book_id']);
                            return $data;
                        },
                        $this->bookMapper->fetchAll()
                    );
                },
            ],
            'Author' => [
                'books' => function ($root, $args, $context) {
                    [$v, $type, $authorId] = explode('|', base64_decode($root['id']));

                    $books = $this->bookMapper->fetchAllForAuthor($authorId);
                    // manual paging
                    $totalCount = count($books);
                    $after = $args['after'] ?? $books[0]->getBookId();
                    $count = $args['first'] ?? $totalCount;

                    // find first book to select
                    $first = 0;
                    foreach ($books as $index => $book) {
                        if ($book->getBookId() === $after) {
                            $first = $index;
                            break;
                        }
                    }

                    // select the books
                    $selected = array_slice($books, $first, $count);

                    // map into AuthorBooksEdge
                    $list = array_map(
                        static function (Book $book) {
                            $data = $book->getArrayCopy();
                            $data['datePublished'] = $data['date_published'];
                            $data['id'] = base64_encode('1|Book|' . $data['author_id']);
                            $data['__typename'] = 'Book';
                            unset($data['date_published'], $data['book_id']);
                            return [
                                'cursor' => $data['id'],
                                'node' => $data,
                            ];
                        },
                        $selected
                    );

                    // create PageInfo
                    $endIndex = count($selected) - 1 ;
                    $pageInfo = [
                        'startCursor' => $selected[0]->getBookId(),
                        'endCursor' => $selected[$endIndex]->getBookId(),
                        'hasNextPage' => $books[($totalCount - 1)]->getBookId() !== $selected[$endIndex]->getBookId(),
                        'hasPreviousPage' => $books[0]->getBookId() !== $selected[0]->getBookId(),
                    ];

                    // return an AuthorBooksConnection
                    return [
                        'totalCount' => $totalCount,
                        'pageInfo' => $pageInfo,
                        'edges' => $list
                    ];
                },
            ],
            'Book' => [
                'author' => function ($root, $args, $context) {
                    return $root['id'];
                },
            ],
        ];
    }

    /**
     * Resolve fields by looking up in database
     */
    public function fieldResolver($source, $args, $context, ResolveInfo $info)
    {
        $fieldName = $info->fieldName;

        if (is_null($fieldName)) {
            throw new \Exception('Could not get $fieldName from ResolveInfo');
        }

        if (is_null($info->parentType)) {
            throw new \Exception('Could not get $parentType from ResolveInfo');
        }

        $parentTypeName = $info->parentType->name;

        try {
            if (isset($this->resolvers[$parentTypeName])) {
                $resolver = $this->resolvers[$parentTypeName];

                if (is_array($resolver)) {
                    if (array_key_exists($fieldName, $resolver)) {
                        $value = $resolver[$fieldName];

                        return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                    }

                    $this->logger->warning('No child resolver for ' . $fieldName . ' in ' . $parentTypeName);
                }

                if (is_object($resolver)) {
                    if (isset($resolver->{$fieldName})) {
                        $value = $resolver->{$fieldName};

                        return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                    }

                    $this->logger->warning('No child object resolver for ' . $fieldName . ' in ' . $parentTypeName);
                }
            } else {
                $this->logger->warning('No resolver for ' . $parentTypeName);
            }
        } catch(\Throwable $e) {
            $this->logger->alert($e->getMessage(), ['exception' => (string)$e]);
            throw $e;
        }
        return Executor::defaultFieldResolver($source, $args, $context, $info);
    }
}
