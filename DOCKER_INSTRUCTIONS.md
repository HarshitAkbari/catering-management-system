# Docker Setup Instructions

## Prerequisites
- Docker and Docker Compose installed.

## Setup Steps

1. **Configure Environment Variables**
   - Copy `.env.example` to `.env` if you haven't already: `cp .env.example .env`
   - Update `.env` with the following database credentials:
     ```ini
     DB_CONNECTION=mysql
     DB_HOST=db
     DB_PORT=3306
     DB_DATABASE=laravel
     DB_USERNAME=root
     DB_PASSWORD=secret  # Or whatever you set in docker-compose.yml
     ```

2. **Build and Start Containers**
   ```bash
   docker-compose up -d --build
   ```

3. **Install Dependencies & Migrations**
   - The `entrypoint.sh` script should handle `composer install` and migrations automatically on startup.
   - You can check logs with: `docker-compose logs -f app`

4. **Access the Application**
   - Open [http://localhost](http://localhost) in your browser.

## Troubleshooting
- If you encounter permission issues with `storage` or `bootstrap/cache`, run:
  ```bash
  docker-compose exec app chmod -R 775 storage bootstrap/cache
  ```
