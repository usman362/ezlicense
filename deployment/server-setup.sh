#!/bin/bash
# ============================================================
# SecureLicence — EC2 Server Setup Script
# Run this ONCE on a fresh Ubuntu 22.04/24.04 EC2 instance
# Usage: sudo bash server-setup.sh
# ============================================================

set -euo pipefail

echo "=========================================="
echo " SecureLicence — EC2 Server Setup"
echo "=========================================="

# ── 1. System Update ────────────────────────────────────────
echo "[1/8] Updating system packages..."
apt update && apt upgrade -y

# ── 2. Install PHP 8.3 + Extensions ────────────────────────
echo "[2/8] Installing PHP 8.3..."
apt install -y software-properties-common
add-apt-repository -y ppa:ondrej/php
apt update

apt install -y \
    php8.3-fpm \
    php8.3-cli \
    php8.3-mysql \
    php8.3-pgsql \
    php8.3-sqlite3 \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-intl \
    php8.3-bcmath \
    php8.3-redis \
    php8.3-opcache \
    php8.3-tokenizer \
    php8.3-fileinfo

# Optimize PHP-FPM for production
cat > /etc/php/8.3/fpm/conf.d/99-securelicence.ini << 'EOF'
; SecureLicence PHP Production Settings
upload_max_filesize = 20M
post_max_size = 25M
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
expose_php = Off
EOF

# ── 3. Install Nginx ────────────────────────────────────────
echo "[3/8] Installing Nginx..."
apt install -y nginx

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# ── 4. Install Composer ─────────────────────────────────────
echo "[4/8] Installing Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ── 5. Install Node.js 20 LTS + npm ────────────────────────
echo "[5/8] Installing Node.js 20..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# ── 6. Install Certbot for SSL ──────────────────────────────
echo "[6/8] Installing Certbot..."
apt install -y certbot python3-certbot-nginx

# ── 7. Create web user & directory ──────────────────────────
echo "[7/8] Setting up directories..."
mkdir -p /var/www/securelicence
mkdir -p /var/www/certbot

# Create deploy user (if not exists)
if ! id "deploy" &>/dev/null; then
    useradd -m -s /bin/bash deploy
    usermod -aG www-data deploy
    echo "deploy ALL=(ALL) NOPASSWD: /usr/sbin/service nginx reload, /usr/sbin/service php8.3-fpm reload" >> /etc/sudoers.d/deploy
    chmod 0440 /etc/sudoers.d/deploy
fi

chown -R deploy:www-data /var/www/securelicence
chmod -R 775 /var/www/securelicence

# ── 8. Install additional tools ─────────────────────────────
echo "[8/8] Installing utilities..."
apt install -y \
    git \
    unzip \
    supervisor \
    acl \
    htop \
    ufw

# ── Firewall Setup ──────────────────────────────────────────
echo "Setting up firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# ── Download AWS RDS SSL Certificate ────────────────────────
echo "Downloading RDS SSL certificate..."
curl -sS https://truststore.pki.rds.amazonaws.com/global/global-bundle.pem \
    -o /etc/ssl/certs/rds-combined-ca-bundle.pem

# ── Restart Services ────────────────────────────────────────
systemctl restart php8.3-fpm
systemctl restart nginx
systemctl enable php8.3-fpm
systemctl enable nginx
systemctl enable supervisor

echo ""
echo "=========================================="
echo " Server setup COMPLETE!"
echo "=========================================="
echo ""
echo " Next steps:"
echo " 1. Copy nginx config:  cp deployment/nginx-securelicence.conf /etc/nginx/sites-available/securelicence"
echo " 2. Enable site:        ln -s /etc/nginx/sites-available/securelicence /etc/nginx/sites-enabled/"
echo " 3. Get SSL cert:       bash deployment/ssl-setup.sh"
echo " 4. Deploy code:        bash deployment/deploy.sh"
echo ""
