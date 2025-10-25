-- Initialize database and a user; this file is run by mysql:5.7 image on startup if mounted appropriately
CREATE DATABASE IF NOT EXISTS hospital_care DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'hc_user'@'%' IDENTIFIED BY 'hc_password';
GRANT ALL PRIVILEGES ON hospital_care.* TO 'hc_user'@'%';
FLUSH PRIVILEGES;
