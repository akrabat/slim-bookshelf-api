<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\Migrations\AbstractMigration;

/**
 * Authors table
 */
class Version20170128183030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add author table';
    }

    /**
     * @param Schema $schema
     * @throws SchemaException
     */
    public function up(Schema $schema): void
    {
        $table = $schema->createTable("author");
        $table->addColumn("author_id", "guid");
        $table->addColumn("name", "string", ["length" => 100]);
        $table->addColumn("biography", "text", ["notnull" => false]);
        $table->addColumn("date_of_birth", "date", ["notnull" => false]);
        $table->addColumn("created", "date");
        $table->addColumn("updated", "date");

        $table->setPrimaryKey(["author_id"]);
    }

    /**
     * @param Schema $schema
     * @throws SchemaException
     */
    public function down(Schema $schema): void
    {
        $schema->dropTable('author');
    }
}
