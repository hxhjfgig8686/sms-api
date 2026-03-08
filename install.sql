-- install.sql
CREATE DATABASE IF NOT EXISTS sms_api;
USE sms_api;

-- جدول المستخدمين (للـ API)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    api_key VARCHAR(64) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول الأرقام (الكومبوات)
CREATE TABLE numbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(20) UNIQUE NOT NULL,
    country_code VARCHAR(10),
    is_used BOOLEAN DEFAULT 0,
    is_deleted BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_country (country_code),
    INDEX idx_used (is_used)
);

-- جدول الرسائل (الأكواد)
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number_id INT,
    number VARCHAR(20) NOT NULL,
    message TEXT,
    otp_code VARCHAR(20),
    service VARCHAR(50),
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_number (number),
    INDEX idx_otp (otp_code),
    FOREIGN KEY (number_id) REFERENCES numbers(id) ON DELETE CASCADE
);

-- جدول المهام (للإشعارات)
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50),
    data TEXT,
    is_done BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول السجلات
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(100),
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول مشرفي الموقع
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin','moderator') DEFAULT 'moderator',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إضافة مستخدم تجريبي للـ API
INSERT INTO users (username, api_key) VALUES 
('test_user', 'sk_cc1480ac5e3a4818e07fb4b0674bc2a72228372220dba26ac4579cfd4eda903b');

-- إضافة مشرف للموقع
INSERT INTO admins (username, password, email, role) VALUES 
('admin', '$2y$10$YourHashedPasswordHere', 'admin@localhost.com', 'admin');

-- إضافة بعض الأرقام التجريبية
INSERT INTO numbers (number, country_code, is_used) VALUES
('201234567890', '20', 0),
('201234567891', '20', 0),
('966501234567', '966', 0),
('971501234567', '971', 0),
('15551234567', '1', 0);