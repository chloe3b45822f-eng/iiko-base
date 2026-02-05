# Руководство по использованию Docker

## Быстрый старт с Docker

Docker позволяет запустить весь проект локально одной командой.

### Предварительные требования

- Docker Desktop установлен на вашем компьютере
- Docker Compose установлен (обычно идет с Docker Desktop)

### Установка Docker

**Windows/Mac:**
1. Скачайте Docker Desktop с https://www.docker.com/products/docker-desktop
2. Установите и запустите

**Linux (Ubuntu):**
```bash
sudo apt-get update
sudo apt-get install docker.io docker-compose
sudo systemctl start docker
sudo systemctl enable docker
```

### Запуск проекта

1. **Клонируйте репозиторий:**
   ```bash
   git clone https://github.com/dovezukatmn/iiko-base.git
   cd iiko-base
   ```

2. **Запустите Docker Compose:**
   ```bash
   docker-compose up -d
   ```

   Это запустит:
   - PostgreSQL на порту 5432
   - Python Backend на порту 8000
   - Nginx на порту 80

3. **Проверьте, что все работает:**
   ```bash
   # Проверка статуса контейнеров
   docker-compose ps

   # Должны быть запущены: iiko-postgres, iiko-backend, iiko-nginx
   ```

4. **Откройте в браузере:**
   - API Docs: http://localhost:8000/docs
   - Health Check: http://localhost:8000/health
   - API через Nginx: http://localhost/api/v1/menu

### Полезные команды

**Просмотр логов:**
```bash
# Все логи
docker-compose logs -f

# Только backend
docker-compose logs -f backend

# Только база данных
docker-compose logs -f postgres
```

**Остановка:**
```bash
# Остановить все контейнеры
docker-compose stop

# Остановить и удалить контейнеры
docker-compose down

# Удалить контейнеры и volumes (БД будет очищена!)
docker-compose down -v
```

**Перезапуск:**
```bash
# Перезапустить все
docker-compose restart

# Перезапустить только backend
docker-compose restart backend
```

**Выполнение команд в контейнере:**
```bash
# Зайти в bash контейнера backend
docker-compose exec backend bash

# Выполнить Python команду
docker-compose exec backend python -m pip list

# Подключиться к PostgreSQL
docker-compose exec postgres psql -U iiko_user -d iiko_db
```

**Просмотр базы данных:**
```bash
# Подключение к PostgreSQL
docker-compose exec postgres psql -U iiko_user -d iiko_db

# Внутри psql:
\dt          # Список таблиц
\d users     # Структура таблицы users
SELECT * FROM menu_items;  # Выборка данных
\q           # Выход
```

### Разработка с Docker

При работе с Docker:

1. **Файлы backend автоматически синхронизируются** - изменения видны сразу (hot reload)
2. **База данных сохраняется** в Docker volume между перезапусками
3. **Изолированное окружение** - не конфликтует с другими проектами

### Обновление зависимостей

**Python:**
```bash
# Обновить requirements.txt
# Затем пересобрать образ
docker-compose build backend
docker-compose up -d backend
```

### Миграции базы данных

```bash
# Выполнить SQL скрипт
docker-compose exec postgres psql -U iiko_user -d iiko_db -f /docker-entrypoint-initdb.d/schema.sql
```

### Backup и восстановление

**Создание backup:**
```bash
docker-compose exec postgres pg_dump -U iiko_user iiko_db > backup.sql
```

**Восстановление:**
```bash
cat backup.sql | docker-compose exec -T postgres psql -U iiko_user -d iiko_db
```

### Проблемы и решения

**Порт уже занят:**
```bash
# Измените порты в docker-compose.yml
ports:
  - "8001:8000"  # Вместо 8000:8000
```

**Контейнер не запускается:**
```bash
# Проверьте логи
docker-compose logs backend

# Пересоздайте контейнер
docker-compose up -d --force-recreate backend
```

**База данных не инициализируется:**
```bash
# Удалите volume и создайте заново
docker-compose down -v
docker-compose up -d
```

### Production deployment

Для production не используйте docker-compose.yml напрямую:

1. Создайте `docker-compose.prod.yml` с production настройками
2. Используйте переменные окружения из .env
3. Настройте правильные пароли и секреты
4. Используйте reverse proxy (Nginx) снаружи Docker

**Пример:**
```bash
docker-compose -f docker-compose.prod.yml up -d
```

## Заключение

Docker упрощает разработку и тестирование проекта локально. Для production рекомендуется использовать установку напрямую на VPS (см. INSTALLATION.md).
