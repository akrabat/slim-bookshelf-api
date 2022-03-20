<?php
namespace App\Bookshelf;

use PDO;
use Psr\Log\LoggerInterface;

class BookMapper
{
    public function __construct(protected LoggerInterface $logger, protected PDO $db)
    {
    }

    /**
     * Fetch all books
     *
     * @return list<Book>
     */
    public function fetchAll(): array
    {
        $sql = "SELECT * FROM book ORDER BY date_published DESC";
        $stmt = $this->db->query($sql);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Book($row);
        }

        return $results;
    }

    /**
     * Fetch all books for an author
     *
     * @return list<Book>
     */
    public function fetchAllForAuthor(string $authorId): array
    {
        $sql = "SELECT * FROM book WHERE author_id = :author_id ORDER BY date_published DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['author_id' => $authorId]);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Book($row);
        }

        return $results;
    }

    /**
     * Load a single book by title
     */
    public function loadByTitle(string $title): Book|false
    {
        $sql = "SELECT * FROM book WHERE title LIKE :title";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['title' => $title]);
        $data = $stmt->fetch();

        if ($data) {
            return new Book($data);
        }

        return false;
    }

    /**
     * Load a single book
     */
    public function loadById(string $id): Book|false
    {
        $sql = "SELECT * FROM book WHERE book_id = :book_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['book_id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Book($data);
        }

        return false;
    }

    /**
     * Create a book
     */
    public function insert(Book $book): Book
    {
        $data = $book->getArrayCopy();
        $data['created'] = date('Y-m-d H:i:s');
        $data['updated'] = $data['created'];

        $query = "INSERT INTO book (book_id, author_id, title, isbn, synopsis, date_published, created, updated)
            VALUES (:book_id, :author_id, :title, :isbn, :synopsis, :date_published, :created, :updated)";
        $stmt = $this->db->prepare($query);
        $stmt->execute($data);

        return new Book($data);
    }

    /**
     * Update a book
     */
    public function update(Book $book): Book
    {
        $data = $book->getArrayCopy();
        $data['updated'] = date('Y-m-d H:i:s');

        $query = "UPDATE book
            SET author_id = :author_id,
                title = :title,
                isbn = :isbn,
                synopsis = :synopsis,
                date_published = :date_published,
                created = :created,
                updated = :updated
            WHERE book_id = :book_id
            ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($data);

        return new Book($data);
    }

    /**
     * Delete a book
     */
    public function delete(string $id): bool
    {
        $data['book_id'] = $id;
        $query = "DELETE FROM book WHERE book_id = :book_id";

        $stmt = $this->db->prepare($query);
        $stmt->execute($data);

        return (bool)$stmt->rowCount();
    }
}
