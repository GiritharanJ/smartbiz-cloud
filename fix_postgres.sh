#!/bin/bash
# fix_postgres.sh - Run this script to automatically fix PostgreSQL issues

echo "🔧 SmartBiz PostgreSQL Fix Script"
echo "=================================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root (use sudo)"
    exit
fi

echo "1. Installing PostgreSQL PHP extensions..."
apt-get update
apt-get install -y php-pgsql php-pdo-pgsql

echo "2. Restarting Apache..."
systemctl restart apache2

echo "3. Checking PostgreSQL status..."
systemctl status postgresql --no-pager

echo "4. Setting up database..."
sudo -u postgres psql <<EOF
ALTER USER postgres WITH PASSWORD 'postgres';
CREATE DATABASE smartbiz;
\c smartbiz;
GRANT ALL PRIVILEGES ON DATABASE smartbiz TO postgres;
EOF

echo "5. Updating pg_hba.conf..."
PG_CONF=$(sudo -u postgres psql -t -P format=unaligned -c 'SHOW hba_file;')
cp $PG_CONF ${PG_CONF}.backup

cat > /tmp/pg_hba.conf <<EOF
# TYPE  DATABASE        USER            ADDRESS                 METHOD
local   all             all                                     trust
host    all             all             127.0.0.1/32            trust
host    all             all             ::1/128                 trust
local   replication     all                                     trust
host    replication     all             127.0.0.1/32            trust
host    replication     all             ::1/128                 trust
EOF

cp /tmp/pg_hba.conf $PG_CONF

echo "6. Restarting PostgreSQL..."
systemctl restart postgresql

echo "✅ Fix script completed!"
echo "Now visit: http://smartbiz.local/setup_database.php"
