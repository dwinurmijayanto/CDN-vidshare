FROM php:8.2-apache

# Enable mod_rewrite & headers
RUN a2enmod rewrite headers

# PHP settings untuk streaming video
RUN echo 'output_buffering = Off' > /usr/local/etc/php/conf.d/streaming.ini \
    && echo 'zlib.output_compression = Off' >> /usr/local/etc/php/conf.d/streaming.ini \
    && echo 'implicit_flush = On' >> /usr/local/etc/php/conf.d/streaming.ini \
    && echo 'max_execution_time = 0' >> /usr/local/etc/php/conf.d/streaming.ini \
    && echo 'max_input_time = -1' >> /usr/local/etc/php/conf.d/streaming.ini \
    && echo 'memory_limit = 128M' >> /usr/local/etc/php/conf.d/streaming.ini

# Apache: izinkan .htaccess
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Options -Indexes\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/vidshare.conf \
    && a2enconf vidshare

# Copy semua file ke web root
COPY . /var/www/html/
RUN rm -f /var/www/html/Dockerfile \
           /var/www/html/.user.ini \
           /var/www/html/render.yaml \
           /var/www/html/entrypoint.sh

# Entrypoint script — set PORT saat runtime
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000

CMD ["/entrypoint.sh"]
