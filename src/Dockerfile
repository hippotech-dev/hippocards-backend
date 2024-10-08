FROM php:8.2-fpm

RUN apt-get update

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libpq-dev \ 
    libjpeg62-turbo-dev \
    libxml2-dev \
    libzip-dev \
    libgd-dev \
    libonig-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql xml zip gd mbstring

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN docker-php-ext-install pdo pdo_mysql

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory permissions
COPY --chown=www:www ./src /var/www/html

COPY --chown=www:www ./entrypoints/app.entrypoint.sh /usr/local/bin/app.entrypoint
RUN chmod u+x /usr/local/bin/app.entrypoint

# Change current user to www
USER www

RUN composer install
RUN php artisan optimize

# USER $user
CMD ["/usr/local/bin/app.entrypoint"]
