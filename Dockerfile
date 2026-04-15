# ============================================================
# Stage 1 – PHP dependencies
# ============================================================
FROM dunglas/frankenphp:php8.2.30-bookworm AS vendor

# install-php-extensions is bundled with the FrankenPHP image
RUN install-php-extensions \
    gd \
    sodium \
    curl \
    mbstring \
    pdo \
    pdo_mysql \
    openssl \
    dom \
    fileinfo \
    filter \
    hash \
    pcre \
    session \
    tokenizer \
    xml \
    ctype \
    zip \
    intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy only the files Composer needs first (layer-cache friendly)
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-scripts \
    --prefer-dist

# Copy the full application source
COPY . .

# Generate the optimised autoloader now that all source files are present
RUN composer dump-autoload --optimize --no-dev --no-scripts

# ============================================================
# Stage 2 – Final runtime image
# ============================================================
FROM dunglas/frankenphp:php8.2.30-bookworm

# Re-install extensions in the final image
RUN install-php-extensions \
    gd \
    sodium \
    curl \
    mbstring \
    pdo \
    pdo_mysql \
    openssl \
    dom \
    fileinfo \
    filter \
    hash \
    pcre \
    session \
    tokenizer \
    xml \
    ctype \
    zip \
    intl

WORKDIR /app

# Copy the fully-built application from the vendor stage
COPY --from=vendor /app /app

# Ensure storage and cache directories exist and are writable
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Laravel production optimisations
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80 443

# FrankenPHP serves the application from public/index.php by default
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
