# VANSCAPE PROJECT

Vanscape is a web application with an interactive map that lets users **save and share vanlife spots** with friends — a simple and private alternative to public platforms.

## Table of Contents

- [Stack](#stack)
- [Local development](#local-development)
  - [Requirements](#requirements)
  - [Setup, Build & Run the Project](#setup-build--run-the-project)
  - [Backend and database](#backend-and-database)
  - [TLS Certificates](#tls-certificates)
  - [Frontend](#frontend)
  - [PgAdmin](#pgadmin)
- [Endpoints](#endpoints)
- [Contributing](#contributing)

## Stack

- Backend : Symfony 7.1 (PHP 8.3)
- Frontend : React 18.3

## Local development

### Requirements

- Install [Docker][1] and [Docker Compose][2] (v2.10+)
- Install [Make][3]

For Mac (and Linux) users, I suggest installing and using [Homebrew][4], a package manager that makes it easy and fast to install a wide range of software :
- [Docker][5]
- [Docker Compose][6]
- [Make][7]

### Setup, Build & Run the Project

1. Clone the repo
   ```bash
   git clone <repository-url or ssh>
   ```

2. Copy the existing .env.example to .env.local and customize the variables (ask your team for the values)

   ```bash
   cp .env.example .env.local
   ```

3. Build fresh images and start the containers
   ```bash
   make all
   ```

   After running `make all`, the following containers should be up and running (`docker ps`):
   - php: [https://localhost](https://localhost)
   - frontend: [http://localhost:5173](http://localhost:5173)
   - pgadmin: [http://localhost:5050](http://localhost:5050)
   - database: PostgreSQL container (not directly exposed to the host)

You can check all the make commands available with :
   ```bash
   make
   ```

### Backend and database

- Check the database connection (returns "OK" if successful) :
  ```bash
  make connection
  ```

- Execute the migration files :
  ```bash
  ## Open a terminal session in php container
  make php-sh

  ## Execute migrations
  php bin/console doctrine:migrations:migrate
  ```

- Generate the SSL keys for JWT Authentication :
  ```bash
  ## Still inside the php container
  php bin/console lexik:jwt:generate-keypair
  ```

- Open [https://localhost](https://localhost) in your browser.
- Accept the auto-generated TLS certificate if prompted. For help, see this [StackOverflow answer](https://stackoverflow.com/a/15076602/1352334).


### TLS Certificates

With a standard installation, the authority used to sign certificates generated in the Caddy container is not trusted by your local machine.
For local development, you must add the authority to the trust store of the host :

```
# Mac
$ docker cp $(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
# Linux
$ docker cp $(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates
# Windows
$ docker compose cp php:/data/caddy/pki/authorities/local/root.crt %TEMP%/root.crt && certutil -addstore -f "ROOT" %TEMP%/root.crt
```

### Frontend

1. Open [http://localhost:5173](http://localhost:5173) in your browser.

### PgAdmin

You can use PgAdmin for development if needed

1. Open [http://localhost:5050](http://localhost:5050) in your web browser.

2. Log in with the credentials defined in the `.env.local` file (email and password).

After logging, you can add your database to pgAdmin.

1. Right-click "Servers" in the top-left corner and select "Create" -> "Server..."
2. Name your connection
3. Change to the "Connection" tab and add the connection details:

- Hostname: "database"
- Port: "5432"
- Maintenance Database: $POSTGRES_DB (see .env.local)
- Username: $POSTGRES_USER (see .env.local)
- Password: $POSTGRES_PASSWORD (see .env.local)

## Endpoints

- All endpoint are described on an openapi format [here](/backend/docs/api/openapi.yaml).
- You can also use swagger UI interface [here](https://localhost/openapi) — only available in local development when the container is running.

## Contributing

- Backend : to contribute to the project, please follow the [contributing guide](/backend/docs/CONTRIBUTING.md). This document references code architecture, and guidelines for the project.
- Frontend : TODO

--- 

For more information :

- [Docker base setup for PostgreSQL & pgAdmin (awesome-compose)](https://github.com/docker/awesome-compose/tree/master/postgresql-pgadmin)
- [Docker base setup for Symfony (dunglas)](https://github.com/dunglas/symfony-docker)


[1]: https://docs.docker.com/get-docker/
[2]: https://docs.docker.com/compose/install/
[3]: https://www.gnu.org/software/make/
[4]: https://brew.sh/
[5]: https://formulae.brew.sh/formula/docker
[6]: https://formulae.brew.sh/formula/docker-compose
[7]: https://formulae.brew.sh/formula/make
