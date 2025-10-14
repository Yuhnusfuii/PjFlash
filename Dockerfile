# ---------- Stage 1: Build frontend assets with Node 20 ----------
FROM node:20-alpine AS assets
WORKDIR /app

# Copy files needed for vite build
COPY package*.json ./
COPY vite.config.* ./
COPY tailwind.config.* postcss.config.* ./
COPY resources ./resources

# Install deps & build
RUN npm install --no-audit --no-fund
RUN npm run build

# ---------- Stage 2: PHP 8.2 runtime ----------
FROM php:8.2-cli-alpine

# Install PHP extensions we need (mysql)
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
# Copy full app source
COPY . .

# Install PHP deps for production
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress

# Bring built assets from Stage 1
COPY --from=assets /app/public/build /app/public/build

# Railway provides $PORT
ENV PORT=8080

# Start Laravel
CMD php artisan storage:link || true && \
    php artisan config:cache && php artisan route:cache && php artisan view:cache && \
    php artisan migrate --force && \
    php -S 0.0.0.0:$PORT -t public server.php
