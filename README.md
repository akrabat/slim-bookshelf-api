# Slim Bookshelf API

A simple [Slim Framework][1] API.

## Branches

* [slim3](https://github.com/akrabat/slim-bookshelf-api/tree/slim3) for a Slim 3 version with OAuth and a website.
* [slim4](https://github.com/akrabat/slim-bookshelf-api) for a Slim 4 version with both HTTP and a GraphQL APIs.

## Install

1. Run the migrations:

        $ composer migrate


2. Load the `api/db/fixtures/default.sql` into your database:

        $ cat api/db/fixtures/default.sql | sqlite3 api/db/bookshelf.db


3. Run the API:

        $ php -S 0.0.0.0:8888 -t public/


4. Access the API:

   * HTTP: `http://localhost:8888/authors`
   * GraphQL: `http://localhost:8888/graphql` - [GraphiQL][2]/[Postman][3]/[Paw][4] compatible for exploration


[1]: https://www.slimframework.com
[2]: https://chrome.google.com/webstore/detail/graphiql-extension/jhbedfdjpmemmbghfecnaeeiokonjclb
[3]: https://www.postman.com
[4]: https://paw.cloud
