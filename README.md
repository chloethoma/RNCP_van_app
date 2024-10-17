# RNCP_van_app

## Getting Started

### Prerequisites
1. Clone the repo
   ```bash
   git clone <repository-url or ssh>
   ```
2. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)

### Setup
1. Create a `.env.local` file and configure the values which are in the `.env` file
2. To build fresh images
   ```bash
   docker compose build --no-cache
   ```
3. To start the containers
   ```bash
   docker compose up --pull always -d --wait
   ```
4. To stop and remove the Docker containers
   ```bash
   docker compose down --remove-orphans
   ```

---

### PgAdmin Setup
1. Open [http://localhost:5050](http://localhost:5050) in your web browser.
   
2. Log in with the credentials defined in the `.env` file (email and password).

3. Add the PostgreSQL database to pgAdmin:

   - Right-click "Servers" in the top-left corner and select "Create" -> "Server...".
   - Name your connection.
   - Go to the "Connection" tab and configure the following details:
     - Hostname: `postgres` (Docker will resolve the container's IP by its name)
     - Port: `5432`
     - Maintenance Database: `$POSTGRES_DB` (from `.env`)
     - Username: `$POSTGRES_USER` (from `.env`)
     - Password: `$POSTGRES_PW` (from `.env`)

---

### Backend Setup

1. Open [https://localhost](https://localhost) in your browser.
   
2. Accept the auto-generated TLS certificate if prompted. For help, see this [StackOverflow answer](https://stackoverflow.com/a/15076602/1352334).

---

### Frontend Setup

1. To enable hot-reloading during development, run:
   ```bash
   docker compose up --watch
   ```

2. Open [http://localhost:5173](http://localhost:5173) in your browser.