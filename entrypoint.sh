#!/bin/bash
# Set PORT dari Render (default 10000)
PORT=${PORT:-10000}

# Update Apache ports.conf dengan PORT yang benar
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

echo "[entrypoint] Apache listening on port $PORT"

# Start Apache
exec apache2-foreground
