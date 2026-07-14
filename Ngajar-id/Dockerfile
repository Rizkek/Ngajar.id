FROM php:8.2-fpm

# Install system dependencies & PostgreSQL driver (karena pakai Supabase)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install Laravel Dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Configure Nginx for Laravel
RUN echo "server { \
    listen 8000; \
    server_name localhost; \
    root /var/www/public; \
    index index.php index.html index.htm; \
    location / { \
        try_files \$uri \$uri/ /index.php?\$query_string; \
    } \
    location ~ \.php$ { \
        include snippets/fastcgi-php.conf; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name; \
        include fastcgi_params; \
    } \
    location ~ /\.ht { \
        deny all; \
    } \
}" > /etc/nginx/sites-available/default

# Expose port 8000 for Koyeb
EXPOSE 8000

# Start PHP-FPM and Nginx
CMD php-fpm -D && nginx -g "daemon off;"
