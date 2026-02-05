#!/bin/bash

###############################################################################
# Скрипт для восстановления из резервной копии
# 
# Использование: ./restore.sh /path/to/backup.sql.gz
###############################################################################

set -e

if [ "$#" -ne 1 ]; then
    echo "Использование: $0 /path/to/backup.sql.gz"
    exit 1
fi

BACKUP_FILE=$1
DB_NAME="iiko_db"
DB_USER="iiko_user"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "Файл backup не найден: $BACKUP_FILE"
    exit 1
fi

echo "Восстановление базы данных из: $BACKUP_FILE"
echo "ВНИМАНИЕ: Это удалит все текущие данные в базе $DB_NAME"
read -p "Продолжить? (y/n) " -n 1 -r
echo

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Отменено"
    exit 1
fi

# Остановка backend сервиса
echo "Остановка backend..."
systemctl stop iiko-backend

# Восстановление
echo "Восстановление базы данных..."
gunzip -c $BACKUP_FILE | psql -U $DB_USER -d $DB_NAME

if [ $? -eq 0 ]; then
    echo "База данных успешно восстановлена!"
else
    echo "Ошибка при восстановлении!"
    exit 1
fi

# Запуск backend сервиса
echo "Запуск backend..."
systemctl start iiko-backend

echo "Готово!"
