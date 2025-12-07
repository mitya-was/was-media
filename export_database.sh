#!/bin/bash
set -e

echo "🗄️  WAS MEDIA - ЭКСПОРТ И ОПТИМИЗАЦИЯ БД"
echo "========================================"
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

OPTIMIZED_DIR="/Users/mitya/Documents/WAS/__WAS_Site_3.0__/production_optimized"

echo -e "${YELLOW}📥 Экспорт базы данных...${NC}"
docker exec was_mysql80 mysqldump -u"${MYSQL_USER:-was}" -p"${MYSQL_PASSWORD:-change-me}" \
  --single-transaction \
  --quick \
  --lock-tables=false \
  --skip-comments \
  was > "$OPTIMIZED_DIR/database.sql"

echo -e "${GREEN}✅ База данных экспортирована${NC}"
echo ""

# Размер БД
DB_SIZE=$(du -sh "$OPTIMIZED_DIR/database.sql" | awk '{print $1}')
echo "Размер БД: $DB_SIZE"
echo ""

echo -e "${YELLOW}🔧 Оптимизация БД (удаление ревизий, корзины, спама)...${NC}"
echo "Это может занять 1-2 минуты..."
echo ""

# Подключиться к БД и оптимизировать
docker exec -i was_mysql80 mysql -u"${MYSQL_USER:-was}" -p"${MYSQL_PASSWORD:-change-me}" "${MYSQL_DATABASE:-was}" << 'EOF'
-- Удалить старые ревизии (оставить только 5 последних на пост)
DELETE FROM b_w_posts 
WHERE post_type = 'revision' 
AND post_parent IN (
    SELECT * FROM (
        SELECT DISTINCT post_parent 
        FROM b_w_posts 
        WHERE post_type = 'revision'
    ) AS parents
)
AND ID NOT IN (
    SELECT * FROM (
        SELECT p1.ID 
        FROM b_w_posts p1
        INNER JOIN (
            SELECT post_parent, MAX(post_modified) as max_date
            FROM b_w_posts
            WHERE post_type = 'revision'
            GROUP BY post_parent
        ) p2 ON p1.post_parent = p2.post_parent
        WHERE p1.post_type = 'revision'
        ORDER BY p1.post_modified DESC
        LIMIT 5
    ) AS keep_revisions
);

-- Удалить корзину
DELETE FROM b_w_posts WHERE post_status = 'trash';
DELETE FROM b_w_postmeta WHERE post_id NOT IN (SELECT ID FROM b_w_posts);

-- Удалить спам комментарии
DELETE FROM b_w_comments WHERE comment_approved = 'spam';
DELETE FROM b_w_commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM b_w_comments);

-- Удалить старые transients (кэш)
DELETE FROM b_w_options WHERE option_name LIKE '_transient_%';
DELETE FROM b_w_options WHERE option_name LIKE '_site_transient_%';

-- Оптимизировать таблицы
OPTIMIZE TABLE b_w_posts;
OPTIMIZE TABLE b_w_postmeta;
OPTIMIZE TABLE b_w_options;
OPTIMIZE TABLE b_w_comments;
OPTIMIZE TABLE b_w_commentmeta;
OPTIMIZE TABLE b_w_terms;
OPTIMIZE TABLE b_w_term_taxonomy;
OPTIMIZE TABLE b_w_term_relationships;

SELECT 'Оптимизация завершена!' AS Status;
EOF

echo ""
echo -e "${GREEN}✅ БД оптимизирована${NC}"
echo ""

echo -e "${YELLOW}📥 Повторный экспорт оптимизированной БД...${NC}"
docker exec was_mysql80 mysqldump -u"${MYSQL_USER:-was}" -p"${MYSQL_PASSWORD:-change-me}" \
  --single-transaction \
  --quick \
  --lock-tables=false \
  --skip-comments \
  was > "$OPTIMIZED_DIR/database.sql"

echo -e "${GREEN}✅ Оптимизированная БД экспортирована${NC}"
echo ""

# Новый размер
DB_SIZE_NEW=$(du -sh "$OPTIMIZED_DIR/database.sql" | awk '{print $1}')
echo "Размер после оптимизации: $DB_SIZE_NEW"
echo ""

echo -e "${GREEN}🎉 ЭКСПОРТ ЗАВЕРШЕН!${NC}"
echo ""
echo "Файл: $OPTIMIZED_DIR/database.sql"
echo ""
