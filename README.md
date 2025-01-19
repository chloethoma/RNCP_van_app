# VANSCAPE PROJECT

## Getting Started

### Prerequisites
1. Clone the repo
   ```bash
   git clone <repository-url or ssh>
   ```
2. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)

### Setup
1. Create a `.env` file and configure the values which are in the `.env.example` file
2. To build fresh images and start the containers 
   ```bash
   make all
   ```
You can check all the make commands available with :
   ```bash
   make
   ```
---

### PgAdmin
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
---

### Backend
1. Open [https://localhost](https://localhost) in your browser.
   
2. Accept the auto-generated TLS certificate if prompted. For help, see this [StackOverflow answer](https://stackoverflow.com/a/15076602/1352334).

#### Database creation and migrations
   * Check the connection to the database ("OK" is return if the connection is working) :
      ```bash
      make connection
      ```
   * Install dependancies
      ```bash
      ## Open a terminal session in php container
      make php-sh

      ## Install
      composer install
      ```

   * Database creation :
      ```bash
      ## Open a terminal session in php container
      make php-sh

      ## Create database
      php bin/console doctrine:database:create
      ```
   * After the creation, you need to execute the migrations files. This command need to be execute each time you need to update your database :
      ```bash
      php bin/console doctrine:migrations:migrate
      ```

#### TLS Certificates
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

---

### Frontend
1. Open [http://localhost:5173](http://localhost:5173) in your browser.

--- 
For more informations :  
- [Docker base setup for PostgreSQL & pgAdmin (awesome-compose)](https://github.com/docker/awesome-compose/tree/master/postgresql-pgadmin)  
- [Docker base setup for Symfony (dunglas)](https://github.com/dunglas/symfony-docker)  