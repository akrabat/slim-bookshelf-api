# Slim Bookshelf API

A simple [Slim Framework][1] API with OAuth 2 authentication.

[1]: https://www.slimframework.com


There are two different apps:

1. **`api`** - This is the API itself with various endpoints.

2. **`web`** - This is a first party website that allows a user to authenticate third party apps.


## Running the apps:

1. Run the migrations:

        $ composer migrate

2. Load the `api/db/fixtures/default.sql` into your database:

        $ cat api/db/fixtures/default.sql | sqlite3 api/db/bookshelf.db

3. Run the API:

        $ php -S 0.0.0.0:8888 -t public/

4. Run the Website:

        $ php -S 0.0.0.0:8889 -t public/

## Authentication via OAuth 2

The `api` protects it data and requires an OAuth2 `Bearer` token to access the
data enpoints (`authors`).

It supports both Password grant for 1st party apps (user enters their username
and password) and the Authorisation Code grant (user authorises the 3rd party 
app on our web site and the app then get a token it can use).

### Logging in

The relevant credentials are:

1st party client: client_id: `mywebsite` client_secret: `abcdef`
3rd party client: client_id: `testclient` client_secret: `abcdef`
Test user: username: `rob` password: `123456`

### Password grant

To log in with the user's credentials directly, we use the `password`
grant type against the `/token` endpoint. We need to specify the client's
credentials along with the user's:

    $ curl -X POST http://localhost:8888/token \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d $'{
            "grant_type": "password",
            "client_id": "mywebsite",
            "client_secret": "abcdef",
            "username": "rob",
            "password": "123456"
        }'
        

If it succeeds, you will get a response containing JSON like this:

    {
      "access_token": "5be4bdb0f09c9405f4beadd4d087cd8c5d66aa36",
      "expires_in": 3600,
      "token_type": "Bearer",
      "scope": null,
      "refresh_token": "8b3c07ee9c0c48078ce8a29e413c6497d399dd8e"
    }


You can then access the API by including the `access_token` in the 
`Authorization` header:

    $ curl http://localhost:8888/authors \
     -H "Authorization: Bearer 5be4bdb0f09c9405f4beadd4d087cd8c5d66aa36" \
     -H "Accept: application/json"


### Authorisation Code grant

Go the website with this URL:

[http://localhost:8889/authorise?response_type=code&client_id=testclient&redirect_uri=http%3A%2F%2Ffake&state=1234][2]

You will be redirected to the login page, log in using the Test user's credentials
which will take you back to the Authorise 3rd Party Application page.

Click "Yes" and the authorisation code will be displayed. The code looks something
like this: `64eae4125c8ce6589daf06d0c20a7aa97a5aff55`.

You can now get a token from the API's `/token` endpoint:


    $ curl -X "POST" http://localhost:8888/token \
      -H "Accept: application/json" \
      -H "Content-Type: application/json" \
      -d $'{
       "grant_type": "authorization_code",
       "client_id": "testclient",
       "client_secret": "abcdef",
       "code": "64eae4125c8ce6589daf06d0c20a7aa97a5aff55",
       "redirect_uri": "http://fake"
      }'

If it succeeds, you will get a response containing JSON like this:

    {
      "access_token": "df7fcb455efb9a2c9544b3ec422ecff50b083b13",
      "expires_in": 3600,
      "token_type": "Bearer",
      "scope": null,
      "refresh_token": "bb87ffbef191bdda55b16257bc36f6d4f445a9a5"
    }

You can then access the API by including the `access_token` in the 
`Authorization` header:

    $ curl http://localhost:8888/authors \
     -H "Authorization: Bearer df7fcb455efb9a2c9544b3ec422ecff50b083b13" \
     -H "Accept: application/json"


[2]: http://localhost:8889/authorise?response_type=code&client_id=testclient&redirect_uri=http%3A%2F%2Ffake&state=1234



