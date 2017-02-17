-- Turn off foreign key checks when truncating tables
PRAGMA foreign_keys = OFF;
DELETE FROM author;
DELETE FROM book;
DELETE FROM oauth_clients;
DELETE FROM oauth_access_tokens;
DELETE FROM oauth_authorization_codes;
DELETE FROM oauth_refresh_tokens;
DELETE FROM oauth_users;
DELETE FROM oauth_scopes;
DELETE FROM oauth_jwt;
PRAGMA foreign_keys = ON;

