# Menggunakan PHP sebagai base image
FROM php:8.2-cli

# Memperbarui paket dan menginstall beberapa tools
RUN apt update && apt install -y \
    curl \
    nano \
    net-tools \
    iputils-ping \
    zip \
    telnet \
    libpq-dev

# Install PostgreSQL PDO driver
RUN docker-php-ext-install pdo pdo_pgsql

# Installing composer
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Membuat directory baru
RUN mkdir /home/app

# Pindah ke directory baru
RUN cd /home/app

# Set working directory
WORKDIR /home/app

# Menambahakn Port Publish
EXPOSE 8000

# Menjalankan php server ketika container di-start
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
