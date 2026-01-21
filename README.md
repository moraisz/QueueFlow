# Queue Flow

Application to manage queues in real-time with a simple and intuitive interface.

## Stacks:

- Frontend/Backend: PHP (Without Framework)
- Database: PostgreSQL
- Web Server: FrankenPHP (Using: Worker Mode, Early Hints, HTTP/2)
- Containerization: Docker

## Features:

- Can be used like API or Web Application.

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
    docker-compose up
    ```

5. Access the application in your web browser at:
    ```bash
    localhost:80
    # or
    https://localhost
    ```
    The browser may warn about a self-signed certificate; you can safely ignore this for local development.

## To Do:

- Implement a migration system for database schema changes.
- Connect to PostgreSQL.
- Add user authentication and authorization.
- See about Mercure in FrankenPHP to real-time.
- Finish API functionality.
- Finish frontend.
