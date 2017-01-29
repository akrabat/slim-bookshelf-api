-- Turn off foreign key checks when truncating tables
PRAGMA foreign_keys = OFF;
DELETE FROM author;
DELETE FROM book;
PRAGMA foreign_keys = ON;

