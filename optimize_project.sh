#!/bin/bash
set -e

echo "🚀 WAS MEDIA - ОПТИМИЗАЦИЯ ПРОЕКТА ДЛЯ PRODUCTION"
echo "================================================"
echo ""

# Цвета для вывода
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Пути
PROJECT_DIR="/Users/mitya/Documents/WAS/__WAS_Site_3.0__"
WP_DIR="$PROJECT_DIR/wordpress"
OPTIMIZED_DIR="$PROJECT_DIR/production_optimized"
TEMP_WP="$OPTIMIZED_DIR/wordpress_temp"

echo -e "${YELLOW}📁 Создание временной копии WordPress...${NC}"
rm -rf "$TEMP_WP"
cp -R "$WP_DIR" "$TEMP_WP"

echo -e "${GREEN}✅ Копия создана${NC}"
echo ""

# 1. Удаление логов
echo -e "${YELLOW}🧹 Удаление логов и временных файлов...${NC}"
find "$TEMP_WP" -name "*.log" -type f -delete
find "$TEMP_WP" -name "debug.log" -type f -delete
find "$TEMP_WP" -name ".DS_Store" -type f -delete
find "$TEMP_WP" -name "Thumbs.db" -type f -delete
find "$TEMP_WP" -name "*.tmp" -type f -delete
echo -e "${GREEN}✅ Логи удалены${NC}"
echo ""

# 2. Очистка кэша
echo -e "${YELLOW}🧹 Очистка кэша...${NC}"
rm -rf "$TEMP_WP/wp-content/cache"
rm -rf "$TEMP_WP/wp-content/uploads/cache"
rm -rf "$TEMP_WP/wp-content/w3tc-config"
echo -e "${GREEN}✅ Кэш очищен${NC}"
echo ""

# 3. Удаление неактивных тем (оставляем только wasmedia и twentytwentyfour)
echo -e "${YELLOW}🎨 Очистка тем...${NC}"
cd "$TEMP_WP/wp-content/themes"
for theme in */; do
    theme_name="${theme%/}"
    if [ "$theme_name" != "wasmedia" ] && [ "$theme_name" != "twentytwentyfour" ] && [ "$theme_name" != "twentytwentythree" ]; then
        echo "  Удаление темы: $theme_name"
        rm -rf "$theme_name"
    fi
done
echo -e "${GREEN}✅ Темы очищены${NC}"
echo ""

# 4. Список плагинов для проверки
echo -e "${YELLOW}📦 Список установленных плагинов:${NC}"
cd "$TEMP_WP/wp-content/plugins"
ls -1
echo ""
echo -e "${YELLOW}⚠️  ВАЖНО: Проверьте список плагинов выше${NC}"
echo "Неактивные плагины можно удалить вручную из:"
echo "$TEMP_WP/wp-content/plugins/"
echo ""

# 5. Создание wp-config-docker.php
echo -e "${YELLOW}⚙️  Создание wp-config-docker.php...${NC}"
cat > "$TEMP_WP/wp-config-docker.php" << 'EOF'
<?php
/**
 * WAS Media - Docker Production Config
 * Создано автоматически для деплоя
 */

// ** Database ** //
define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'was');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'was');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'change-me');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'mysql');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// ** Redis Object Cache ** //
define('WP_REDIS_HOST', getenv('REDIS_HOST') ?: 'redis');
define('WP_REDIS_PORT', 6379);
define('WP_REDIS_TIMEOUT', 1);
define('WP_REDIS_READ_TIMEOUT', 1);
define('WP_REDIS_DATABASE', 0);

// ** Performance ** //
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');
define('WP_POST_REVISIONS', 5);
define('AUTOSAVE_INTERVAL', 300);
define('EMPTY_TRASH_DAYS', 7);

// ** Debug (ВЫКЛЮЧЕНО на production!) ** //
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

// ** WordPress URLs ** //
define('WP_HOME', 'https://was.media');
define('WP_SITEURL', 'https://was.media');

// ** Security ** //
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', false);
define('FORCE_SSL_ADMIN', true);

// ** Auto Updates ** //
define('WP_AUTO_UPDATE_CORE', 'minor');

// ** Security Keys - ВАЖНО: Скопируйте из вашего текущего wp-config.php ** //
// TODO: Заменить на ваши ключи!
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

// ** Table Prefix ** //
$table_prefix = 'b_w_';

// ** Absolute Path ** //
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
EOF

echo -e "${GREEN}✅ wp-config-docker.php создан${NC}"
echo -e "${RED}⚠️  ВАЖНО: Скопируйте security keys из вашего wp-config.php в wp-config-docker.php!${NC}"
echo ""

# 6. Статистика
echo -e "${YELLOW}📊 Статистика оптимизации:${NC}"
echo ""
echo "Оригинальный размер:"
du -sh "$WP_DIR" | awk '{print "  " $1}'
echo ""
echo "Оптимизированный размер:"
du -sh "$TEMP_WP" | awk '{print "  " $1}'
echo ""

# 7. Переименование
echo -e "${YELLOW}📦 Финализация...${NC}"
rm -rf "$OPTIMIZED_DIR/wordpress"
mv "$TEMP_WP" "$OPTIMIZED_DIR/wordpress"
echo -e "${GREEN}✅ Оптимизированный WordPress готов в:${NC}"
echo "  $OPTIMIZED_DIR/wordpress"
echo ""

echo -e "${GREEN}🎉 ОПТИМИЗАЦИЯ ЗАВЕРШЕНА!${NC}"
echo ""
echo -e "${YELLOW}СЛЕДУЮЩИЕ ШАГИ:${NC}"
echo "1. Скопируйте security keys из wp-config.php в wp-config-docker.php"
echo "2. Проверьте список плагинов и удалите неактивные"
echo "3. Запустите скрипт экспорта БД: ./export_database.sh"
echo "4. Запустите скрипт создания архива: ./create_archive.sh"
echo ""
