<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;

/**
 * Book table
 */
class Version20170128183204 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $conn = $this->connection;
        if ($conn->getDatabasePlatform() instanceof SqlitePlatform) {
            // Migrations doesn't support SQLite foreign keys, so do it manually
            $this->addSql('PRAGMA foreign_keys = ON');

            $this->addSql('CREATE TABLE book (book_id CHAR(36) NOT NULL, author_id CHAR(36) NOT NULL, title VARCHAR(100) NOT NULL, isbn VARCHAR(13) DEFAULT NULL, synopsis CLOB DEFAULT NULL, date_published DATE DEFAULT NULL, created DATE NOT NULL, updated DATE NOT NULL, PRIMARY KEY(book_id), FOREIGN KEY(author_id) REFERENCES author(author_id));');
        } else {
            $table = $schema->createTable("book");
            $table->addColumn("book_id", "guid");
            $table->addColumn("author_id", "guid");
            $table->addColumn("title", "string", ["length" => 100]);
            $table->addColumn("isbn", "string", ["length" => 13, "notnull" => false]);
            $table->addColumn("synopsis", "text", ["notnull" => false]);
            $table->addColumn("date_published", "date", ["notnull" => false]);
            $table->addColumn("created", "date");
            $table->addColumn("updated", "date");

            $table->setPrimaryKey(["book_id"]);
            $table->addForeignKeyConstraint('author', ['author_id'], ['author_id'], [], 'fk_author_book');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('book');
    }
}
