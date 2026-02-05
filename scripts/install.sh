#!/bin/bash

###############################################################################
# Скрипт установки iiko-base на Jino VPS
# 
# Этот скрипт автоматически устанавливает и настраивает:
# - Python 3.10+
# - PHP 8.1+
# - PostgreSQL
# - Nginx
# - Необходимые зависимости
###############################################################################

set -e  # Остановка при ошибке

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Функции для вывода
print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Проверка прав суперпользователя
if [ "$EUID" -ne 0 ]; then 
    print_error "Пожалуйста, запустите скрипт с правами суперпользователя (sudo)"
    exit 1
fi

print_info "Начало установки iiko-base..."

# Обновление системы
print_info "Обновление системных пакетов..."
apt-get update
apt-get upgrade -y

# Установка PostgreSQL
print_info "Установка PostgreSQL..."
apt-get install -y postgresql postgresql-contrib

# Запуск PostgreSQL
systemctl start postgresql
systemctl enable postgresql

# Установка Python и pip
print_info "Установка Python..."
apt-get install -y python3 python3-pip python3-venv python3-dev

# Установка PHP и необходимых модулей
print_info "Установка PHP и модулей..."
apt-get install -y php php-fpm php-pgsql php-mbstring php-xml php-curl php-zip php-cli

# Установка Composer
print_info "Установка Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка Nginx
print_info "Установка Nginx..."
apt-get install -y nginx

# Установка дополнительных инструментов
print_info "Установка дополнительных инструментов..."
apt-get install -y git curl wget unzip

# Создание пользователя для приложения
print_info "Создание пользователя iiko..."
if ! id "iiko" &>/dev/null; then
    useradd -m -s /bin/bash iiko
    print_info "Пользователь iiko создан"
else
    print_warning "Пользователь iiko уже существует"
fi

# Создание директорий
print_info "Создание директорий приложения..."
mkdir -p /var/www/iiko-base
chown -R iiko:iiko /var/www/iiko-base

# Настройка PostgreSQL
print_info "Настройка PostgreSQL..."
sudo -u postgres psql -c "CREATE USER iiko_user WITH PASSWORD 'change_this_password';" || print_warning "Пользователь уже существует"
sudo -u postgres psql -c "CREATE DATABASE iiko_db OWNER iiko_user;" || print_warning "База данных уже существует"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE iiko_db TO iiko_user;"

# Настройка firewall (если установлен ufw)
if command -v ufw &> /dev/null; then
    print_info "Настройка firewall..."
    ufw allow 'Nginx Full'
    ufw allow OpenSSH
fi

print_info "Установка базовых компонентов завершена!"
echo ""
print_info "Следующие шаги:"
echo "1. Склонируйте репозиторий в /var/www/iiko-base"
echo "2. Запустите скрипт setup.sh для настройки окружения"
echo "3. Запустите скрипт deploy.sh для деплоя приложения"
echo ""
print_warning "Не забудьте изменить пароли в файлах .env!"
