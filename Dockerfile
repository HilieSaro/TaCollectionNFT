Dockerfile
# Image PHP officielle avec Apache
FROM php:8.2-apache

# Copier tout le projet dans le dossier web
COPY . /var/www/html/

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html

# Exposer le port utilisé par Render
EXPOSE 10000

# Apache écoute par défaut sur 80, on le redirige vers 10000 pour Render
CMD ["apache2-foreground"]