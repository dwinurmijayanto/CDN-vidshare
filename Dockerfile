FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite headers

# Install curl extension (untuk proxy.php)
RUN docker-php-ext-install curl 2>/dev/null || true \
    && apt-get update -qq \
    && apt-get install -y --no-install-recommends libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Copy semua file ke web root
COPY . /var/www/html/

# Hapus file yang tidak perlu
RUN rm -f /var/www/html/Dockerfile \
           /var/www/html/.user.ini \
           /var/www/html/render.yaml \
           /var/www/html/.htaccess

# Apache config: izinkan .htaccess & set document root
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Options -Indexes\n\
    Require all granted\n\
</Directory>\n\
\n\
# Matikan buffering untuk streaming video\n\
<IfModule mod_deflate.c>\n\
    SetEnvIfNoCase Request_URI \\.php$ no-gzip dont-vary\n\
</IfModule>' > /etc/apache2/conf-available/vidshare.conf \
    && a2enconf vidshare

# PHP settings untuk streaming
RUN echo 'output_buffering = Off\n\
zlib.output_compression = Off\n\
implicit_flush = On\n\
max_execution_time = 0\n\
max_input_time = -1\n\
memory_limit = 128M' > /usr/local/etc/php/conf.d/streaming.ini

# Render menggunakan PORT env variable
RUN sed -i 's/Listen 80/Listen ${PORT:-80}/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT:-80}>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
