# Объявляем все цели как фальшивые (не имена файлов)
.PHONY: create up install migrate seed down php

# 🔥 Полная установка проекта одной командой
create:
	@echo Копирование .env
	cp .env.example .env
	@echo "Запуск контейнеров..."
	docker compose up -d
	@echo "Установка зависимостей..."
	docker compose exec -T php composer install --no-interaction
	@echo "Применение миграций..."
	docker compose exec -T php php artisan migrate --force
	@echo "Заполнение базы данными..."
	docker compose exec -T php php artisan db:seed --force
	@echo ""
	@echo "Готово! Приложение доступно: http://localhost:8000"

# Запуск контейнеров
up:
	docker compose up -d

# Установка зависимостей (ручной режим)
install:
	docker compose exec php composer install

# Миграции БД (ручной режим)
migrate:
	docker compose exec php php artisan migrate

# Seed БД (ручной режим)
seed:
	docker compose exec php php artisan db:seed

# Остановка контейнеров
down:
	docker compose down

# Вход в контейнер
php:
	docker compose exec php /bin/bash