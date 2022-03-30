FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    software-properties-common \
    lsb-release \
    apt-transport-https \
    ca-certificates \
    wget \
    gnupg2

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (some are already compiled in the PHP base image)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd json zip xml

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create myuser
RUN useradd -G www-data,root -u 1000 -d /home/myuser myuser
RUN mkdir -p /home/myuser/.composer && \
    chown -R myuser:myuser /home/myuser

# Set working directory
WORKDIR /web/

USER $user
