FROM php:7.4-apache

# 古いDebianパッケージの取得先をアーカイブに変更
RUN sed -i \
    -e 's|deb.debian.org/debian-security|archive.debian.org/debian-security|g' \
    -e 's|deb.debian.org/debian|archive.debian.org/debian|g' \
    /etc/apt/sources.list \
    && sed -i '/bullseye-updates/d' /etc/apt/sources.list \
    && printf 'Acquire::Check-Valid-Until "false";\n' \
       > /etc/apt/apt.conf.d/99archive

# CakePHP3に必要なPHP拡張機能をインストール
RUN rm -rf /var/lib/apt/lists/* \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        unzip \
        git \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        mbstring \
        zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composerをインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 作業ディレクトリ
WORKDIR /var/www/html

# Apacheの公開ディレクトリをCakePHPのwebrootに変更
RUN sed -i \
    's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/webroot|' \
    /etc/apache2/sites-available/000-default.conf

# CakePHPのURL書き換えを有効化
RUN printf '<Directory /var/www/html/webroot>\nAllowOverride All\nRequire all granted\n</Directory>\n' \
    > /etc/apache2/conf-available/cakephp.conf \
    && a2enconf cakephp