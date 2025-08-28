
FROM composer:2 AS composer

FROM php:8.2-cli AS base
LABEL authors="niji-dsf"

## COMPOSER
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH=/composer/vendor/bin:$PATH
COPY --from=composer /usr/bin/composer /usr/bin/composer
## END COMPOSER

## PACKAGE DEBIAN
RUN set -eux; \
    apt-get update && apt-get install -y \
        git \
        unzip \
        p7zip-full \
        libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*
## END PACKAGE DEBIAN

## ADD SSH
RUN apt-get update && apt-get install -y openssh-client \
    && rm -rf /var/lib/apt/lists/*
## END ADD SSH

#ENTRYPOINT ["composer", "install"]
CMD ["tail", "-f", "/dev/null"]

FROM base AS dev



#Add map user
RUN set -x ; \
  groupadd -g 1000 admin ; \
  useradd -u 1000 -g admin -m admin;

#Add user & composer folder
RUN set -x ; \
  mkdir -p /composer; \
  mkdir -p /composer-cache; \
  chown -R 1000:www-data /composer; \
  chown -R 1000:www-data /composer-cache;

## COMPOSER
ENV COMPOSER_HOME=/composer
ENV COMPOSER_CACHE_DIR=/composer-cache
## END COMPOSER

# Configuration des permissions
RUN mkdir -p /var/www/html; \
    chown -R admin:admin /var/www/html

# Set working directory for project content
WORKDIR /var/www/html

# Switch to admin user
USER admin