# Banking API

This project is a simple banking API built with Laravel that uses SQLite as the database. It allows users to create accounts, deposit, withdraw, transfer funds, and check balances. The project is set up to run in a Docker container for easy setup and deployment.

## Requirements

-   Docker
-   Docker Compose

## Getting Started

### 1. Clone the Repository

First, clone the repository to your local machine:

```bash
git clone https://github.com/h-alves/ebanx-assignment.git
cd ebanx-assignment
```

### 2. Build and Run Docker Containers

Build and start the Docker containers using `docker-compose`:

```bash
docker-compose up -d --build
```

This command will:

-   Build the Docker images based on the configurations in your `Dockerfile` and `docker-compose.yml`.
-   Start the containers in detached mode (`-d`).

### 3. Access the Docker Container

Access the running Docker container for the application:

```bash
docker-compose exec app bash
```

This command opens a bash shell inside the `app` container.

### 4. Install Composer Dependencies

Inside the Docker container, run the following command to install the PHP dependencies:

```bash
composer install
```

### 5. Configure Environment File

Create the .env file from the example:

```bash
cp .env.example .env
```

### 6. Run Migrations

Run the database migrations to create the necessary tables:

```bash
php artisan migrate
```

### 7. Start the Application

The application should already be running inside the Docker container. You can access it at `http://localhost:8000`.

### Additional Docker Commands

-   **Stop Docker Containers:**

    ```bash
    docker-compose down
    ```

-   **View Container Logs:**

    ```bash
    docker-compose logs
    ```

-   **Restart Containers:**

    ```bash
    docker-compose restart
    ```

-   **Rebuild Containers:**
    ```bash
    docker-compose up -d --build
    ```

## API Endpoints

The following endpoints are available in the API:

-   **Reset state before starting tests**

    -   **URL:** `/reset`
    -   **Method:** `POST`
    -   **Response:** `200 OK`

-   **Get balance for non-existing account**

    -   **URL:** `/balance?account_id=1234`
    -   **Method:** `GET`
    -   **Response:** `404 0`

-   **Create account with initial balance**

    -   **URL:** `/event`
    -   **Method:** `POST`
    -   **Payload:** `{"type":"deposit", "destination":"100", "amount":10}`
    -   **Response:** `201 {"destination": {"id":"100", "balance":10}}`

-   **Deposit into existing account**

    -   **URL:** `/event`
    -   **Method:** `POST`
    -   **Payload:** `{"type":"deposit", "destination":"100", "amount":10}`
    -   **Response:** `201 {"destination": {"id":"100", "balance":20}}`

-   **Get balance for existing account**

    -   **URL:** `/balance?account_id=100`
    -   **Method:** `GET`
    -   **Response:** `200 20`

-   **Withdraw from non-existing account**

    -   **URL:** `/event`
    -   **Method:** `POST`
    -   **Payload:** `{"type":"withdraw", "origin":"200", "amount":10}`
    -   **Response:** `404 0`

-   **Withdraw from existing account**

    -   **URL:** `/event`
    -   **Method:** `POST`
    -   **Payload:** `{"type":"withdraw", "origin":"100", "amount":5}`
    -   **Response:** `201 {"origin": {"id":"100", "balance":15}}`

-   **Transfer from existing account**

    -   **URL:** `/event`
    -   **Method:** `POST`
    -   **Payload:** `{"type":"transfer", "origin":"100", "amount":15, "destination":"300"}`
    -   **Response:** `201 {"origin": {"id":"100", "balance":0}, "destination": {"id":"300", "balance":15}}`

-   **Transfer from non-existing account**
    -   **URL:** `/event`
    -   **Method:** `POST`
    -   **Payload:** `{"type":"transfer", "origin":"200", "amount":15, "destination":"300"}`
    -   **Response:** `404 0`
