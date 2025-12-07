#!/bin/bash
set -e

echo "📦 WAS MEDIA - СОЗДАНИЕ PRODUCTION АРХИВА"
echo "=========================================="
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

OPTIMIZED_DIR="/Users/mitya/Documents/WAS/__WAS_Site_3.0__/production_optimized"

cd "$OPTIMIZED_DIR"

echo -e "${YELLOW}📦 Создание архива WordPress...${NC}"
echo "Это может занять 5-10 минут..."
echo ""

tar -czf wordpress.tar.gz \
  --exclude='wordpress/.git' \
  --exclude='wordpress/node_modules' \
  --exclude='wordpress/.DS_Store' \
  wordpress

echo -e "${GREEN}✅ Архив создан${NC}"
echo ""

echo -e "${YELLOW}🔐 Создание контрольных сумм...${NC}"
shasum -a 256 database.sql > checksums.sha256
shasum -a 256 wordpress.tar.gz >> checksums.sha256

echo -e "${GREEN}✅ Контрольные суммы созданы${NC}"
echo ""

echo -e "${YELLOW}📊 ИТОГОВАЯ СТАТИСТИКА:${NC}"
echo ""
ls -lh database.sql wordpress.tar.gz checksums.sha256
echo ""

echo "Контрольные суммы:"
cat checksums.sha256
echo ""

echo -e "${GREEN}🎉 PRODUCTION ПАКЕТ ГОТОВ!${NC}"
echo ""
echo "Файлы в папке: $OPTIMIZED_DIR"
echo "  - database.sql"
echo "  - wordpress.tar.gz"
echo "  - checksums.sha256"
echo ""
echo -e "${YELLOW}СЛЕДУЮЩИЙ ШАГ:${NC}"
echo "Загрузите файлы на сервер командой:"
echo ""
echo "rsync -avz --progress --partial -e \"ssh -p 2222\" \\"
echo "  database.sql wordpress.tar.gz checksums.sha256 \\"
echo "  root@5.75.183.231:/opt/apps/projects/was-media/"
echo ""
