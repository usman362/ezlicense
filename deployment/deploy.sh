#!/bin/bash
# ============================================================
# SecureLicence — Zero-Downtime Deployment Script
# Usage: bash deploy.sh [branch]
# Default branch: main
# ============================================================

set -euo pipefail

# ── Configuration ───────────────────────────────────────────
APP_DIR="/var/www/securelicence"
REPO_URL="git@github.com:YOUR_USERNAME/ezlicense_new.git"  # UPDATE THIS
BRANCH="${1:-main}"
PHP_SERVICE="php8.3-fpm"
DEPLOY_USER="deploy"

echo "=========================================="
echo " SecureLicence — Deploying branch: $BRANCH"
echo " $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================="

cd "$APP_DIR"

# ── 1. Enable Maintenance Mode ──────────────────────────────
echo "[1/10] Enabling maintenance mode..."
php artisan down --retry=60 --refresh=15 || true

# ── 2. Pull Latest Code ────────────────────────────────────
echo "[2/10] Pulling latest code..."
git fetch origin
git reset --hard origin/$BRANCH

# ── 3. Install Composer Dependencies ────────────────────────
echo "[3/10] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# ── 4. Install NPM Dependencies & Build ────────────────────
echo "[4/10] Building frontend assets..."
npm ci --production=false
npm run build

# ── 5. Run Migrations ──────────────────────────────────────
echo "[5/10] Running database migrations..."
php artisan migrate --force

# ── 6. Clear & Rebuild Caches ──────────────────────────────
echo "[6/10] Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ── 7. Create Storage Link ──────────────────────────────────
echo "[7/10] Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# ── 8. Set Permissions ──────────────────────────────────────
echo "[8/10] Setting permissions..."
chown -R $DEPLOY_USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── 9. Restart Services ────────────────────────────────────
echo "[9/10] Restarting services..."
sudo service $PHP_SERVICE reload
php artisan queue:restart

# ── 10. Disable Maintenance Mode ────────────────────────────
echo "[10/10] Going live!"
php artisan up

echo ""
echo "=========================================="
echo " Deployment COMPLETE!"
echo " $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================="
echo ""
