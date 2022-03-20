<?php
namespace App\Bookshelf;

use PDO;
use Psr\Log\LoggerInterface;

class AuthorMapper
{
    public function __construct(protected LoggerInterface $logger, protected PDO $db)
    {
    }

    /**
     * Fetch all authors
     *
     * @return list<Author>
     */
    public function fetchAll(): array
    {
        $sql = "SELECT * FROM author ORDER BY name";
        $stmt = $this->db->query($sql);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Author($row);
        }

        return $results;
    }

    /**
     * Load a single author by name
     */
    public function loadByName(string $name): Author|false
    {
        $sql = "SELECT * FROM author WHERE name LIKE :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);
        $data = $stmt->fetch();

        if ($data) {
            return new Author($data);
        }

        return false;
    }

    /**
     * Load a single author
     */
    public function loadById(string $id): Author|false
    {
        $sql = "SELECT * FROM author WHERE author_id = :author_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['author_id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Author($data);
        }

        return false;
    }

    /**
     * Create an author
     */
    public function insert(Author $author): Author
    {
        $data = $author->getArrayCopy();
        $data['created'] = date('Y-m-d H:i:s');
        $data['updated'] = $data['created'];

        $query = "INSERT INTO author (author_id, name, biography, date_of_birth, created, updated)
            VALUES (:author_id, :name, :biography, :date_of_birth, :created, :updated)";
        $stmt = $this->db->prepare($query);
        $stmt->execute($data);

        return new Author($data);
    }

    /**
     * Update an author
     */
    public function update(Author $author): Author
    {
        $data = $author->getArrayCopy();
        $data['updated'] = date('Y-m-d H:i:s');

        $query = "UPDATE author
            SET name = :name,
                biography = :biography,
                date_of_birth = :date_of_birth,
                created = :created,
                updated = :updated
            WHERE author_id = :author_id
            ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($data);

        return new Author($data);
    }

    /**
     * Delete an author
     */
    public function delete(string $id): bool
    {
        $data['author_id'] = $id;
        $query = "DELETE FROM author WHERE author_id = :author_id";

        $stmt = $this->db->prepare($query);
        $stmt->execute($data);

        return (bool)$stmt->rowCount();
    }
}
