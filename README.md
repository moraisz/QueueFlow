# Queue Flow

Application to manage queues in real-time with a simple and intuitive interface.

## Stacks:

- Frontend/Backend: PHP (Without Framework)
- Database: PostgreSQL
- Web Server: FrankenPHP
- Containerization: Docker

## Features:

- Can be used like API or Web Application.
- Worker Mode for better performance.
- Early Hints to improve loading times.
- HTTP/2 support.

## Installation:

1. Clone the repository:
   ```bash
   git clone
   ```

2. Navigate to the project directory:
   ```bash
   cd QueueFlow
   ```

3. Copy and configure the environment file:
   ```bash
   cp .env.example .env
   ```
   Modify the `.env` file as needed to set your database credentials and other configurations.

4. Build and run the Docker containers:
    ```bash
    docker compose up
    ```

5. Execute the database migrations to set up the database schema:
    ```bash
    docker compose exec app php bootstrap/migrations.php run
    ```

6. Access the application in your web browser at:
    ```bash
    localhost:80
    # or
    https://localhost
    ```
    The browser may warn about a self-signed certificate; you can safely ignore this for local development.

## To Do:

- Improve SqlQueryBuilder class.
- Improve cli.
- Improve code structure and organization.
- Add user authentication and authorization.
- See about Mercure in FrankenPHP to real-time.
- Finish API functionality.
- Finish frontend.
