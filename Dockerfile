FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
COPY vite.config.* ./
COPY tailwind.config.* postcss.config.* ./
COPY resources ./resources
RUN npm install --no-audit --no-fund
RUN npm run build

FROM php:8.2-cli-alpine
RUN docker-php-ext-install pdo pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress
COPY --from=assets /app/public/build /app/public/build
ENV PORT=8080
CMD php artisan storage:link || true && \
    php artisan config:cache && php artisan route:cache && php artisan view:cache && \
    php artisan migrate --force && \
    php -S 0.0.0.0:$PORT -t public server.php
