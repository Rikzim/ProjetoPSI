FROM yiisoftware/yii2-php:8.1-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/backend/web|g' /etc/apache2/sites-available/000-default.conf

# Add /uploads alias so backend and API can serve images stored in frontend/web/uploads
COPY docker/uploads.conf /etc/apache2/conf-available/uploads.conf
RUN a2enmod alias && a2enconf uploads
