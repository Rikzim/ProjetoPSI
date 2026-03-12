FROM yiisoftware/yii2-php:8.4-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/frontend/web|g' /etc/apache2/sites-available/000-default.conf

# Enable mod_alias (uploads/ is under the document root and served natively)
RUN a2enmod alias
