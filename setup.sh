#!/bin/bash

# ADIKASN Laravel 12 - Quick Setup Script
# Run this script to setup ADIKASN quickly (Linux/Mac only)
# For Windows, follow SETUP.md manually

set -e  # Exit on error

echo "╔════════════════════════════════════════════════════════╗"
echo "║      ADIKASN Laravel 12 - Setup Script                ║"
echo "║      BKPSDM Kabupaten Tabalong                        ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Check PHP
echo "✓ Checking PHP version..."
if ! command -v php &> /dev/null; then
    echo "✗ PHP not found! Please install PHP 8.2+"
    exit 1
fi
PHP_VERSION=$(php -r 'echo phpversion();')
echo "  PHP Version: $PHP_VERSION"

# Check Composer
echo ""
echo "✓ Checking Composer..."
if ! command -v composer &> /dev/null; then
    echo "✗ Composer not found! Please install Composer"
    exit 1
fi
echo "  Composer is installed"

# Check MySQL
echo ""
echo "✓ Checking MySQL..."
if ! command -v mysql &> /dev/null; then
    echo "✗ MySQL not found! Please install MySQL 8.0+"
    exit 1
fi
echo "  MySQL is installed"

echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║        Starting Setup Process...                       ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Step 1: Install Composer packages
echo "1️⃣  Installing Composer packages..."
composer install --no-interaction --prefer-dist
echo "✓ Composer packages installed!"

# Step 2: Generate APP_KEY
echo ""
echo "2️⃣  Generating application key..."
php artisan key:generate
echo "✓ Application key generated!"

# Step 3: Setup .env
echo ""
echo "3️⃣  Setting up .env file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✓ .env file created from template"
else
    echo "✓ .env file already exists"
fi

# Step 4: Database setup
echo ""
echo "4️⃣  Setting up database..."
echo ""
echo "Preparing to create database 'adikasn'..."
echo "Please enter your MySQL password (or press Enter if no password):"

read -s MYSQL_PASSWORD

if [ -z "$MYSQL_PASSWORD" ]; then
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS adikasn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
else
    mysql -u root -p"$MYSQL_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS adikasn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
fi

if [ $? -eq 0 ]; then
    echo "✓ Database 'adikasn' created/verified!"
else
    echo "✗ Failed to create database"
    echo "Please create database manually:"
    echo "  mysql -u root -p"
    echo "  CREATE DATABASE adikasn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    exit 1
fi

# Step 5: Update .env (optional)
echo ""
echo "5️⃣  Updating .env file..."
echo ""
echo "Enter database password (press Enter if no password):"
read -s DB_PASSWORD

if [ -z "$DB_PASSWORD" ]; then
    DB_PASSWORD=""
fi

sed -i.bak "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env
echo "✓ .env file updated!"

# Step 6: Run migrations
echo ""
echo "6️⃣  Running database migrations..."
php artisan migrate:fresh --seed --force
echo "✓ Database migrations completed!"

# Step 7: Create storage link
echo ""
echo "7️⃣  Creating storage symlink..."
php artisan storage:link
echo "✓ Storage symlink created!"

# Step 8: Clear cache
echo ""
echo "8️⃣  Clearing application cache..."
php artisan cache:clear
php artisan config:clear
echo "✓ Cache cleared!"

# Step 9: Set permissions
echo ""
echo "9️⃣  Setting folder permissions..."
chmod -R 775 storage/app/public
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache
echo "✓ Permissions set!"

echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║          Setup Complete! 🎉                           ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
echo "✓ All setup steps completed successfully!"
echo ""
echo "Next steps:"
echo "  1. Run the server:"
echo "     php artisan serve"
echo ""
echo "  2. Open browser:"
echo "     http://localhost:8000"
echo ""
echo "  3. Login with:"
echo "     Admin:  NIP=admin, Password=admin123"
echo "     User:   NIP=user,  Password=user123"
echo ""
echo "For detailed documentation, see:"
echo "  - README.md         : Project overview"
echo "  - SETUP.md          : Detailed setup guide"
echo "  - PROJECT_STRUCTURE.md : File & folder structure"
echo "  - ROUTES.md         : API endpoints documentation"
echo "  - DATABASE.md       : Database schema documentation"
echo ""
