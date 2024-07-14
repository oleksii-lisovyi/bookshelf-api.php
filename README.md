# bookshelf-api.php

## Description

This project is a PHP application which allows you to manage your books & authors entities using REST API.
The application is implemented with PHP Symfony framework and PostgreSQL database for persisting data.

## Project setup

### Pre-requisites

Please follow the instructions below for setting the project on your local environment.

#### DDEV environment

The easiest way to set up the project is to run it with DDEV which is Docker environment orchestration tool.
This way you don't have to have PHP, PostgreSQL or any other required software on your machine.

Obviously you must have Docker installed beforehand. Please
follow [official Docker installation documentation](https://docs.docker.com/engine/install/) if you don't have Docker
installed yet.

Once Docker is installed please
follow [official documentation for DDEV installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/).
Once DDEV is installed you're ready for the next steps of the project startup.

### Step-by-step installation (first time)

1. Assuming you've already git cloned the project into your local machine, but if not yet let's do so using SSH:

   If you don't have SSH configured for your GitHub account please
   follow [GitHub official documentation for setting SSH key up](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/adding-a-new-ssh-key-to-your-github-account?tool=webui).
    ```shell
    cd ~/my/working/directory && git clone git@github.com:oleksii-lisovyi/bookshelf-api.php.git bookshelf && cd bookshelf
    ```
2. All the next steps should be executed from the project root directory.

   Let's start the DDEV project (this usually takes some time in order to download & build all necessary Docker images):
    ```shell
    ddev start
    ```
3. Let's add your user SSH keys into DDEV, so Composer installation process will not require authorization for packages
   downloading:
    ```shell
    ddev auth ssh
    ```
4. Let's install Composer dependencies:
   ```shell
   ddev composer install
   ```
5. It's time to actualize DB by running migrations:
   ```shell
   ddev exec bin/console doctrine:migrations:migrate -n
   ```
   Feel free to remove `-n` option for the CLI command if you prefer to interact with the shell while execution.
6. And that was it! Now you're ready to play around the API endpoints on http://bookshelf.ddev.site base URL.
   Please check out available API endpoints below.
7. Optionally if you want to run the app on HTTPS please follow the instructions [DDEV Trusted HTTPS Certificates](https://ddev.com/blog/ddev-local-trusted-https-certificates/) how to do so. 

## API endpoints

### General information

All the endpoints (except image uploading) are expecting JSON data objects for `POST` & `PUT` requests.
All the endpoints return JSON in response.

### Endpoints

All the endpoints request path are relative to the base URL. 
E.g. for local DDEV environment it's http://bookshelf.ddev.site.

| path                      | method | description                                                       | body                                                                                                                                                                                                                                                                                                                                                                                                                                                          | query params                                                                                                                                   | response                                                                                                                        |
|---------------------------|:------:|-------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------|
| `/books`                  | `POST` | Creates new book.                                                 | JSON object<br>`name`: required, string, up to 255 characters<br>`short_description`: optional, string<br>`published_at`: optional, string, format `YYYY-MM-DD`<br>`authors`: optional, array of:<br>&nbsp;&nbsp;`id`: optional, number, existing Author ID<br>&nbsp;&nbsp;`firstname`: required, string, up to 255 characters<br>&nbsp;&nbsp;`middlename`: optional, string, up to 255 characters<br>&nbsp;&nbsp;`lastname`: required, up to 255 characters; |                                                                                                                                                | Created book full information including authors.                                                                                |
| `/books`                  | `GET`  | Lists created books using pagination.                             |                                                                                                                                                                                                                                                                                                                                                                                                                                                               | `limit`: optional, number, from 1 to 100, defaults to 5<br>`offset`: optional, number, from 1<br>`include_authors`: optional, boolean (1 or 0) | List of created books according to provided pagination params sorted by name alphabetically.                                    |
| `/books/search/by_author` | `GET`  | Lists created books found by author`s first, middle or last name. |                                                                                                                                                                                                                                                                                                                                                                                                                                                               | `q`: required, string.                                                                                                                         | List of created books according to provided search criteria sorted by name alphabetically.                                      |
| `/books/{id}`             | `GET`  | Lists created book by provided ID.                                |                                                                                                                                                                                                                                                                                                                                                                                                                                                               | `include_authors`: optional, boolean (1 or 0)                                                                                                  | Created book full information including authors if specified.                                                                   |
| `/books/{id}`             | `PUT`  | Updates created book by provided ID.                              | JSON object<br>`name`: required, string, up to 255 characters<br>`short_description`: optional, string<br>`published_at`: optional, string, format `YYYY-MM-DD`                                                                                                                                                                                                                                                                                               |                                                                                                                                                | Created book full information including authors.                                                                                |
| `/books/image/{id}`       | `POST` | Uploads an image for a created book.                              | `multipart/form-data` request is expected with `image` field name containing image file. Supported types: `png`, `jpeg`. File size limit: 2 Mb.                                                                                                                                                                                                                                                                                                               |                                                                                                                                                | Created book full information including authors.                                                                                |
| `/authors`                | `POST` | Creates new author.                                               | JSON object<br>`firstname`: required, string, up to 255 characters<br>`middlename`: optional, string, up to 255 characters<br>`lastname`: required, up to 255 characters;<br>`books`: optional, array of:<br>&nbsp;&nbsp;`id`: optional, number of existing book ID<br>&nbsp;&nbsp;`name`: required, string, up to 255 characters<br>&nbsp;&nbsp;`short_description`: optional, string<br>&nbsp;&nbsp;`published_at`: optional, string, format `YYYY-MM-DD`   |                                                                                                                                                | Created author full information including books.                                                                                |
| `/authors`                | `GET`  | Lists created authors using pagination.                           |                                                                                                                                                                                                                                                                                                                                                                                                                                                               | `limit`: optional, number, from 1 to 100, defaults to 10<br>`offset`: optional, number, from 1<br>`include_books`: optional, boolean (1 or 0)  | List of created authors according to provided pagination params sorted by lastname alphabetically including books if specified. |

Check out [Book.http](./app/test/http/Book.http) and [Author.http](./app/test/http/Author.http) files for API request examples for the implemented API endpoints.
There are available some files in [img](./app/test/http/img) directory for testing purposes.

### Book image
Book image (if uploaded) is available by the provided image path relative to base URL. 
E.g. if `image` of a book contains following path `/images/books/small-img1-6692a5701979f524743788.jpg` the image URL
for local DDEV environment with base URL https://bookshelf.ddev.site is available under at the following URL: 
https://bookshelf.ddev.site/images/books/small-img1-6692a5701979f524743788.jpg
