FROM php:8.3-cli

# Install system dependencies for PDO MySQL and mysql-client
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy application source
COPY . .

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public/"]
