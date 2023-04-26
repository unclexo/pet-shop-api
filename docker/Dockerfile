# Builds app using PHP 8.2 FPM
FROM php:8.2-fpm

# Defines working dir in the container
WORKDIR /var/www/html

# Defines system user name
ARG USER

# Installs system dependencies
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev && \
    apt-get install -y \
    git \
    zip \
    unzip \
    sqlite3 \
    openssh-client \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installs PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd

# Creates system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer && chown -R $USER:$USER /home/$USER

# Copies everything from host's current dir to the container's dir specified by WORKDIR
COPY . .

# Installs composer 2.3
COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer

# Sets user
USER $USER

# Starts PHP-FPM
CMD ["php-fpm"]
