CREATE DATABASE business_db;
USE business_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin','user') DEFAULT 'user'
);

CREATE TABLE businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100),
    category VARCHAR(100),
    address VARCHAR(255),
    phone VARCHAR(20),
    description TEXT,
    image VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
