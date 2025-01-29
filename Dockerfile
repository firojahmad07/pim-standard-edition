# Use the official PHP image as the base
FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www/html

# Environment variables
ENV PHP_CONF_DATE_TIMEZONE=UTC \
    PHP_CONF_MAX_EXECUTION_TIME=60 \
    PHP_CONF_MEMORY_LIMIT=512M \
    PHP_CONF_OPCACHE_VALIDATE_TIMESTAMP=0 \
    PHP_CONF_MAX_INPUT_VARS=1000 \
    PHP_CONF_UPLOAD_LIMIT=40M \
    PHP_CONF_MAX_POST_SIZE=40M \
    YARN_GLOBAL_FOLDER=/var/www/.yarn/global \
    YARN_CACHE_FOLDER=/var/www/.yarn/cache

# Update and install PHP extensions and system dependencies
RUN apt-get update && \
    apt-get --yes install --no-install-recommends \
        imagemagick \
        libmagickwand-dev \
        ghostscript \
        openssh-client \
        aspell \
        aspell-en aspell-es aspell-de aspell-fr \
        git \
        curl \
        zip \
        unzip \
        libicu-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libonig-dev \
        libxslt1-dev \
        libzip-dev \
        libmemcached-dev \
        zlib1g-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-configure intl && \
    docker-php-ext-install \
        pdo \
        pdo_mysql \
        intl \
        opcache \
        mysqli \
        exif \
        gd \
        xsl \
        zip \
        mbstring \
        bcmath && \
    pecl install imagick && docker-php-ext-enable imagick && \
    pecl install apcu && docker-php-ext-enable apcu && \
    pecl install memcached && docker-php-ext-enable memcached && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Set PHP memory limit
RUN echo "memory_limit=${PHP_CONF_MEMORY_LIMIT}" > /usr/local/etc/php/conf.d/memory-limit.ini

# Install MySQL client
RUN apt-get update && apt-get install -y default-mysql-client && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js, npm, and Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash - && \
    apt-get install -y nodejs && npm install -g yarn && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Yarn global and cache folders
RUN mkdir -p $YARN_GLOBAL_FOLDER $YARN_CACHE_FOLDER && \
    chown -R www-data:www-data $YARN_GLOBAL_FOLDER $YARN_CACHE_FOLDER && \
    chmod -R 775 $YARN_GLOBAL_FOLDER $YARN_CACHE_FOLDER && \
    yarn config set global-folder $YARN_GLOBAL_FOLDER && \
    yarn config set cache-folder $YARN_CACHE_FOLDER

# Set writable permissions for application and temporary directories
RUN chown -R www-data:www-data /var/www/html /tmp && \
    chmod -R 775 /var/www/html /tmp

# Copy application files
COPY . .

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
