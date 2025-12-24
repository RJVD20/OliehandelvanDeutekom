#!/bin/bash
set -e

# Altijd vanuit project-root
cd "$(dirname "$0")"

echo "🚀 Deploy TEST omgeving gestart"

SSH_HOST="test-oliehandel"

REMOTE_BASE="/home/oliehand/domains/test.oliehandelvandeutekom.nl"
REMOTE_LARAVEL="$REMOTE_BASE/laravel"
REMOTE_PUBLIC="$REMOTE_BASE/public_html"

# =========================
# FRONTEND BUILD
# =========================
echo "🎨 Vite build starten"
npm run build

# =========================
# BACKEND DEPLOY
# =========================
echo "📦 Deploy Laravel backend"

ssh "$SSH_HOST" "
rm -rf $REMOTE_LARAVEL/app
rm -rf $REMOTE_LARAVEL/routes
rm -rf $REMOTE_LARAVEL/resources/
rm -rf $REMOTE_LARAVEL/config
"

scp -r \
  app \
  routes \
  resources/views \
  config \
  "$SSH_HOST:$REMOTE_LARAVEL/"

# =========================
# BUILD ASSETS
# =========================
echo "🎨 Deploy build assets"

ssh "$SSH_HOST" "
rm -rf $REMOTE_LARAVEL/public/build
rm -rf $REMOTE_PUBLIC/build
"

scp -r public/build "$SSH_HOST:$REMOTE_LARAVEL/public/"
ssh "$SSH_HOST" "cp -r $REMOTE_LARAVEL/public/build $REMOTE_PUBLIC/"

# =========================
# STORAGE (IMAGES)
# =========================
echo "🖼️ Deploy storage"

scp -r storage/app/public/* "$SSH_HOST:$REMOTE_PUBLIC/storage/"

# =========================
# CACHE CLEAR
# =========================
echo "🧹 Laravel cache clear"

ssh "$SSH_HOST" "
cd $REMOTE_LARAVEL
php artisan view:clear || true
php artisan config:clear || true
php artisan cache:clear || true
"

echo "✅ Deploy TEST omgeving afgerond"
