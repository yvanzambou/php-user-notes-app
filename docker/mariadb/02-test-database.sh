#!/bin/sh
# Wird vom MariaDB-Container beim allerersten Start ausgeführt (nur Entwicklung).
# Eigene Datenbank für die Tests, damit die Entwicklungsdaten nie überschrieben werden.
mariadb -uroot -p"${MARIADB_ROOT_PASSWORD}" <<SQL
CREATE DATABASE IF NOT EXISTS \`${MARIADB_DATABASE}_test\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`${MARIADB_DATABASE}_test\`.* TO '${MARIADB_USER}'@'%';
FLUSH PRIVILEGES;
SQL
