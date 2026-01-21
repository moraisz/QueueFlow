# Stage 1: PHP Builder
FROM dunglas/frankenphp:1.9.1-builder-php8.4-bookworm AS builder

# Install system dependencies for PHP extensions
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libnss3-tools \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets \
    opcache

# ---------------------------------------------------------------------

# Stage 2: Production Runner
FROM dunglas/frankenphp:1.9.1-php8.4-bookworm AS runner

ARG USER=appuser
ARG UID=1000
ARG GID=1000

# Copy frankenphp binary from builder stage
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp

# Install system dependencies and clean up
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip \
    unzip \
    libpq5 \
    libpng16-16 \
    libonig5 \
    libxml2 \
    libzip4 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP extensions and configuration from builder stage
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copy configuration files
COPY docker/php/custom_prod.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/caddy/Caddyfile.prod /etc/caddy/Caddyfile

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Create system user to run Composer and Artisan Commands
RUN groupadd -g ${GID} ${USER} && \
    useradd -m -u ${UID} -g ${USER} -d /home/${USER} ${USER} && \
    mkdir -p /home/${USER}/.composer && \
    chown -R ${USER}:${USER} /home/${USER} && \
    chown -R ${USER}:${USER} /app /config/caddy /data/caddy

# Copy dependencies files
COPY --chown=${USER}:${USER} composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy application files
COPY --chown=${USER}:${USER} . .

RUN chmod +x /app/docker/start.sh

USER $USER

# Run shell file
CMD ["/app/docker/start.sh"]
