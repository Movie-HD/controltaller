#!/bin/bash

# Quick verification script for Laravel Reverb setup

echo "🔍 Verificando configuración de Laravel Reverb..."
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ Archivo .env no encontrado"
    exit 1
fi

# Check BROADCAST_CONNECTION
if grep -q "BROADCAST_CONNECTION=reverb" .env; then
    echo "✅ BROADCAST_CONNECTION=reverb configurado"
else
    echo "⚠️  BROADCAST_CONNECTION no está configurado como 'reverb'"
    echo "   Agrega: BROADCAST_CONNECTION=reverb"
fi

# Check VITE variables
echo ""
echo "Verificando variables VITE_REVERB_*:"

for var in VITE_REVERB_APP_KEY VITE_REVERB_HOST VITE_REVERB_PORT VITE_REVERB_SCHEME; do
    if grep -q "$var" .env; then
        value=$(grep "^$var=" .env | cut -d '=' -f2-)
        echo "✅ $var=$value"
    else
        echo "❌ $var no encontrado"
    fi
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📋 Variables requeridas en .env:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
cat << 'EOF'

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=1
REVERB_APP_KEY=tu-app-key
REVERB_APP_SECRET=tu-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
EOF

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
