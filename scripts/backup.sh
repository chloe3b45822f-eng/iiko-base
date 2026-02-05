#!/bin/bash

###############################################################################
# Скрипт для создания резервной копии базы данных
# 
# Использование: ./backup.sh
###############################################################################

set -e

# Настройки
BACKUP_DIR="/var/backups/iiko-base"
DB_NAME="iiko_db"
DB_USER="iiko_user"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/backup_${DATE}.sql.gz"
KEEP_DAYS=7

# Создание директории для backup
mkdir -p $BACKUP_DIR

# Создание backup
echo "Создание backup базы данных..."
pg_dump -U $DB_USER $DB_NAME | gzip > $BACKUP_FILE

# Проверка успешности
if [ $? -eq 0 ]; then
    echo "Backup успешно создан: $BACKUP_FILE"
    
    # Удаление старых backup (старше KEEP_DAYS дней)
    find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +$KEEP_DAYS -delete
    echo "Старые backup удалены (старше $KEEP_DAYS дней)"
else
    echo "Ошибка при создании backup!"
    exit 1
fi

# Вывод информации
echo "Размер backup: $(du -h $BACKUP_FILE | cut -f1)"
echo "Всего backup: $(ls -1 $BACKUP_DIR/backup_*.sql.gz | wc -l)"
