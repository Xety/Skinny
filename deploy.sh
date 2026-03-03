#!/bin/bash
set -e

echo "🚀 Deploying Skinny Bot..."

cd /home/forge/Skinny

# Pull code
sudo -u forge git reset --hard
sudo -u forge git clean -df
sudo -u forge git pull origin main

# Install dependencies
sudo -u forge composer install --no-dev --optimize-autoloader

# Clear cache if needed
sudo -u forge rm -rf tmp/*.tmp

# Restart bot
sudo supervisorctl restart skinny-bot

echo "✅ Bot deployed successfully!"