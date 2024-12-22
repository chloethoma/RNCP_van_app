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
You can check all the make commands with :
   ```bash
   make
   ```

---

### PgAdmin Setup
1. Open [http://localhost:5050](http://localhost:5050) in your web browser.
   
2. Log in with the credentials defined in the `.env` file (email and password).

---

### Backend Setup

1. Open [https://localhost](https://localhost) in your browser.
   
2. Accept the auto-generated TLS certificate if prompted. For help, see this [StackOverflow answer](https://stackoverflow.com/a/15076602/1352334).

3. Database creation and migrations : 
   * Check the connection to the database ("OK" is return if the connection is working) :
      ```bash
      make connection
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
---

### Frontend Setup

1. To enable hot-reloading during development, run:
   ```bash
   make watch
   ```

2. Open [http://localhost:5173](http://localhost:5173) in your browser.