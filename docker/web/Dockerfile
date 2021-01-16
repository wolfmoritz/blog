FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get -y install software-properties-common && \
    apt-get install -y \
      apache2 \
      php7.4 \
      php7.4-cli \
      libapache2-mod-php7.4 \
      php-apcu \
      php-xdebug \
      php7.4-gd \
      php7.4-json \
      php7.4-ldap \
      php7.4-mbstring \
      php7.4-mysql \
      # php7.4-pgsql \
      # php7.4-sqlite3 \
      php7.4-xml \
      php7.4-xsl \
      php7.4-zip \
      # php7.4-soap \
      # php7.4-opcache \
      nano \
      sendmail \
      composer \
      php-curl

COPY apache-host.conf /etc/apache2/sites-available/000-default.conf
COPY run.sh /usr/local/bin/run
RUN chmod +x /usr/local/bin/run
COPY wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

EXPOSE 80
