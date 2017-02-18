<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Tables for OAuth 2 server
 *
 * See: https://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */
class Version20170216171524 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql('CREATE TABLE oauth_access_tokens (
            access_token VARCHAR(40) NOT NULL,
            client_id VARCHAR(80) NOT NULL,
            user_id VARCHAR(255),
            expires TIMESTAMP NOT NULL,
            scope VARCHAR(2000),
            CONSTRAINT access_token_pk
            PRIMARY KEY (access_token))
        ');
        $this->addSql('CREATE TABLE oauth_authorization_codes (
            authorization_code VARCHAR(40) NOT NULL,
            client_id VARCHAR(80) NOT NULL,
            user_id VARCHAR(255),
            redirect_uri VARCHAR(2000),
            expires TIMESTAMP NOT NULL,
            scope VARCHAR(2000),
            CONSTRAINT auth_code_pk
            PRIMARY KEY (authorization_code))
        ');
        $this->addSql('CREATE TABLE oauth_clients (
            client_id VARCHAR(80) NOT NULL,
            client_secret VARCHAR(80),
            redirect_uri VARCHAR(2000) NOT NULL,
            grant_types VARCHAR(80),
            scope VARCHAR(100),
            user_id VARCHAR(80),
            CONSTRAINT clients_client_id_pk
            PRIMARY KEY (client_id))
        ');
        $this->addSql('CREATE TABLE oauth_jwt (
            client_id VARCHAR(80) NOT NULL,
            subject VARCHAR(80),
            public_key VARCHAR(2000),
            CONSTRAINT jwt_client_id_pk
            PRIMARY KEY (client_id))
        ');
        $this->addSql('CREATE TABLE oauth_public_keys (
            client_id VARCHAR(80),
            public_key VARCHAR(8000),
            private_key VARCHAR(8000),
            encryption_algorithm VARCHAR(80) DEFAULT "RS256")
        ');
        $this->addSql('CREATE TABLE oauth_refresh_tokens (
            refresh_token VARCHAR(40) NOT NULL,
            client_id VARCHAR(80) NOT NULL,
            user_id VARCHAR(255),
            expires TIMESTAMP NOT NULL,
            scope VARCHAR(2000),
            CONSTRAINT refresh_token_pk
            PRIMARY KEY (refresh_token))
        ');
        $this->addSql('CREATE TABLE oauth_scopes (
            scope TEXT,
            is_default BOOLEAN)
        ');
        $this->addSql('CREATE TABLE oauth_users (
            username VARCHAR(255) NOT NULL,
            password VARCHAR(2000),
            first_name VARCHAR(255),
            last_name VARCHAR(255),
            CONSTRAINT username_pk
            PRIMARY KEY (username))
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('oauth_access_tokens');
        $schema->dropTable('oauth_authorization_codes');
        $schema->dropTable('oauth_clients');
        $schema->dropTable('oauth_jwt');
        $schema->dropTable('oauth_public_keys');
        $schema->dropTable('oauth_refresh_tokens');
        $schema->dropTable('oauth_scopes');
        $schema->dropTable('oauth_users');
    }
}
