FROM php:8.2-fpm

# Install system dependencies + Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libsqlite3-dev \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql pdo_pgsql pdo_sqlite pgsql sqlite3 mbstring exif pcntl bcmath gd zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create application user
RUN useradd -ms /bin/bash -G www-data laravel

# Set working directory
WORKDIR /var/www

# Copy project
COPY . .

# Install frontend dependencies & build assets
RUN npm install && npm run build

# CI can enable dev dependencies by passing --build-arg INSTALL_DEV=true.
ARG INSTALL_DEV=false

# Install Laravel dependencies
RUN if [ "$INSTALL_DEV" = "true" ]; then \
        composer install --no-interaction --prefer-dist --optimize-autoloader; \
    else \
        composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader; \
    fi

# Set permissions
RUN chown -R laravel:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Switch user
USER laravel

# Expose port
EXPOSE 10000

# 🚀 FINAL CMD (with migration + cache clear)
CMD ["sh", "-lc", "php artisan optimize:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000"]