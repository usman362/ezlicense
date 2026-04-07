#!/bin/bash
# ============================================================
# SecureLicence — SSL Certificate Setup (Let's Encrypt)
# Usage: sudo bash ssl-setup.sh
# ============================================================

set -euo pipefail

DOMAIN="securelicence.com"
EMAIL="admin@securelicence.com"  # UPDATE THIS

echo "=========================================="
echo " SSL Setup for $DOMAIN"
echo "=========================================="

# Step 1: Temporarily use HTTP-only nginx config for certificate challenge
echo "[1/3] Preparing for certificate challenge..."

# Create temporary nginx config without SSL
cat > /etc/nginx/sites-available/securelicence-temp << EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }

    location / {
        root /var/www/securelicence/public;
        index index.php;
    }
}
EOF

ln -sf /etc/nginx/sites-available/securelicence-temp /etc/nginx/sites-enabled/securelicence
rm -f /etc/nginx/sites-enabled/securelicence-temp 2>/dev/null || true
nginx -t && systemctl reload nginx

# Step 2: Obtain certificate
echo "[2/3] Obtaining SSL certificate..."
certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    -d $DOMAIN \
    -d www.$DOMAIN \
    --email $EMAIL \
    --agree-tos \
    --non-interactive

# Step 3: Switch to full SSL nginx config
echo "[3/3] Activating SSL configuration..."
cp /var/www/securelicence/deployment/nginx-securelicence.conf /etc/nginx/sites-available/securelicence
ln -sf /etc/nginx/sites-available/securelicence /etc/nginx/sites-enabled/securelicence
rm -f /etc/nginx/sites-available/securelicence-temp

nginx -t && systemctl reload nginx

# Setup auto-renewal
echo "Setting up auto-renewal cron..."
(crontab -l 2>/dev/null; echo "0 3 * * * certbot renew --quiet --post-hook 'systemctl reload nginx'") | sort -u | crontab -

echo ""
echo "=========================================="
echo " SSL Setup COMPLETE!"
echo " https://$DOMAIN is now active"
echo "=========================================="
