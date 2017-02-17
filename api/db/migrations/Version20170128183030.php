<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Authors table
 */
class Version20170128183030 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
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
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('author');
    }
}
