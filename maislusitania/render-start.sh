#!/bin/bash
set -e

# Initialize the application in production mode without prompting
echo "Initializing application..."
php init --env=Production --overwrite=All

# Wait for the database to be ready (optional but recommended)
# Run database migrations
echo "Running migrations..."
php yii migrate --interactive=0

# Ensure directories are writable
chmod -R 777 frontend/web/uploads frontend/runtime backend/runtime console/runtime

# Start Apache in the foreground
echo "Starting Apache..."
apache2-foreground
