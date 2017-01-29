<?php
namespace Bookshelf;

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
}
