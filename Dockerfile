FROM php:8.2-apache

# Atualiza pacotes e instala dependências necessárias para compilar extensões PHP
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    docker-php-ext-enable mysqli pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia os arquivos do projeto
COPY ./www /var/www/html/

# Define o diretório de trabalho
WORKDIR /var/www/html