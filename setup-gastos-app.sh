#!/bin/bash

# Script para crear la estructura del proyecto gastos-app
# Autor: axes
# Fecha: $(date +%Y-%m-%d)

echo "======================================"
echo "Creando estructura de gastos-app"
echo "======================================"
echo ""

# Crear directorios principales
echo "📁 Creando directorios..."

mkdir -p public
mkdir -p app/{Controllers,Models,Services,Core,Config}
mkdir -p storage/documents
mkdir -p routes

echo "✅ Directorios creados"
echo ""

# Crear archivos en public/
echo "📄 Creando archivos en public/..."
touch public/index.php

# Crear archivos en app/Core/
echo "📄 Creando archivos en app/Core/..."
touch app/Core/Router.php
touch app/Core/Controller.php
touch app/Core/Database.php

# Crear archivos en app/Config/
echo "📄 Creando archivos en app/Config/..."
touch app/Config/config.php

# Crear archivos en routes/
echo "📄 Creando archivos en routes/..."
touch routes/web.php

# Crear archivos en raíz
echo "📄 Creando archivos en raíz del proyecto..."
touch .env
touch .env.example
touch composer.json
touch README.md

# Crear .gitignore con contenido básico
echo "📄 Creando .gitignore..."
cat > .gitignore << 'EOF'
# Environment variables
.env

# Composer
/vendor/
composer.lock

# IDE
.vscode/
.idea/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Storage
/storage/documents/*
!/storage/documents/.gitkeep

# Logs
*.log
EOF

# Crear .gitkeep en storage/documents para mantener la carpeta en git
touch storage/documents/.gitkeep

echo "✅ Archivos creados"
echo ""

# Mostrar estructura creada
echo "======================================"
echo "✨ Estructura creada exitosamente"
echo "======================================"
echo ""
echo "Estructura del proyecto:"
tree -L 3 -a 2>/dev/null || find . -not -path '*/\.*' -print | sed -e 's;[^/]*/;|____;g;s;____|; |;g'

echo ""
echo "======================================"
echo "Siguiente paso:"
echo "Agrega tu código a los archivos"
echo "======================================"
