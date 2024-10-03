FROM php:5.6-apache

# Cambiar los repositorios a los archivos de Debian Archive
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list && \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until

# Actualizar los repositorios y limpiar la cache de apt
RUN apt-get update

# Instalar dependencias necesarias
RUN apt-get install -y \
    freetds-dev \
    freetds-bin \
    freetds-common \
    libsybdb5 \
    unixodbc \
    unixodbc-bin \
    libmcrypt-dev

# Crear enlace simbólico para libsybdb
RUN ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/libsybdb.so

# Copiar archivos de configuración ODBC y FreeTDS
COPY ./freetds/odbc.ini /etc/odbc.ini
COPY ./freetds/freetds.ini /etc/freetds/freetds.conf

# Configurar la zona horaria en php.ini
RUN echo "date.timezone = America/Tijuana" > /usr/local/etc/php/conf.d/timezone.ini

# Instalar y habilitar la extensión mssql
RUN docker-php-ext-configure mssql --with-mssql=/usr && \
    docker-php-ext-install mssql

# Limpiar la cache de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Copiar el archivo de configuración de Apache
COPY ./apache2/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copiar la aplicación apps2 al directorio /var/www/html
COPY ./apps2 /var/www/html

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Comando para iniciar Apache en primer plano
CMD ["apache2-foreground"]