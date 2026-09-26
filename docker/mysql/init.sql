-- Create the hospital care database
CREATE DATABASE IF NOT EXISTS hospital_care DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'hc_user'@'%' IDENTIFIED BY 'hc_password';
GRANT ALL PRIVILEGES ON hospital_care.* TO 'hc_user'@'%';
FLUSH PRIVILEGES;
