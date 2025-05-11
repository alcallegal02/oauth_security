# oauth-docker-practice/php-app/Dockerfile
FROM php:8.2-apache

# Instalar la extensión cURL, necesaria para que PHP se comunique con Keycloak
# y otras dependencias si fueran necesarias.
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install -j$(nproc) curl
    # YA NO INTENTAMOS HABILITAR 'session' AQUÍ

# Crear los directorios si no existen y luego copiar el contenido
RUN mkdir -p /var/www/html/vulnerable && mkdir -p /var/www/html/mitigated

COPY vulnerable/ /var/www/html/vulnerable/
COPY mitigated/ /var/www/html/mitigated/

# Opcional: Establecer el propietario correcto si es necesario
RUN chown -R www-data:www-data /var/www/html/vulnerable
RUN chown -R www-data:www-data /var/www/html/mitigated

EXPOSE 80