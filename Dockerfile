FROM php:8.5.4-fpm-alpine3.23

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

RUN echo "DirectoryIndex index.php" > /etc/apache2/conf-enabled/directoryindex.conf
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:10000/g' /etc/apache2/sites-enabled/000-default.conf

EXPOSE 10000

CMD ["apache2-foreground"]
