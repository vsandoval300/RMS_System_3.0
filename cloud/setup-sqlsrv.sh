#!/usr/bin/env bash
set -e

# --- Repositorio Microsoft (Debian 12) ---
sudo install -m 0755 -d /usr/share/keyrings
curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
  | sudo gpg --dearmor -o /usr/share/keyrings/microsoft.gpg

echo "deb [signed-by=/usr/share/keyrings/microsoft.gpg] https://packages.microsoft.com/config/debian/12/prod/ stable main" \
  | sudo tee /etc/apt/sources.list.d/mssql-release.list > /dev/null

# --- Paquetes del sistema ---
sudo apt-get update
sudo ACCEPT_EULA=Y apt-get install -y msodbcsql18 unixodbc-dev

# --- Extensiones PHP ---
# El printf envía 'Enter' a pecl por si pregunta
printf "\n" | sudo pecl install sqlsrv
printf "\n" | sudo pecl install pdo_sqlsrv
sudo docker-php-ext-enable sqlsrv pdo_sqlsrv

