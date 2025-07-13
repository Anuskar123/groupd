-- FreshMart Grocery Store Database
-- CSY2088 Group Project
-- Database Schema

CREATE DATABASE IF NOT EXISTS freshmart_db;
USE freshmart_db;

-- Users table for customer and admin authentication
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(10),
    user_type ENUM('customer', 'admin') DEFAULT 'customer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table for product organization
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    product_name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    unit VARCHAR(20) DEFAULT 'piece',
    image_url VARCHAR(255),
    sku VARCHAR(50) UNIQUE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Shopping cart table
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Orders table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT NOT NULL,
    notes TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Product reviews table
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (first_name, last_name, email, password, user_type) VALUES
('Admin', 'User', 'admin@freshmart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample categories
INSERT INTO categories (category_name, description, image_url) VALUES
('Fruits & Vegetables', 'Fresh fruits and vegetables', 'fruits_vegetables.jpg'),
('Dairy & Eggs', 'Milk, cheese, yogurt and eggs', 'dairy_eggs.jpg'),
('Meat & Seafood', 'Fresh meat and seafood products', 'meat_seafood.jpg'),
('Bakery', 'Fresh bread, cakes and pastries', 'bakery.jpg'),
('Pantry Staples', 'Rice, flour, oil and basic ingredients', 'pantry.jpg'),
('Beverages', 'Soft drinks, juices and water', 'beverages.jpg'),
('Snacks', 'Chips, cookies and snack items', 'snacks.jpg'),
('Personal Care', 'Hygiene and personal care products', 'personal_care.jpg');

-- Insert sample products
INSERT INTO products (category_id, product_name, description, price, stock_quantity, unit, sku, is_featured) VALUES
-- Fruits & Vegetables
(1, 'Fresh Apples', 'Crispy red apples', 250.00, 100, 'kg', 'FV001', TRUE),
(1, 'Bananas', 'Fresh yellow bananas', 120.00, 150, 'kg', 'FV002', FALSE),
(1, 'Tomatoes', 'Fresh red tomatoes', 80.00, 80, 'kg', 'FV003', FALSE),
(1, 'Onions', 'Fresh white onions', 60.00, 200, 'kg', 'FV004', FALSE),
(1, 'Carrots', 'Fresh orange carrots', 90.00, 120, 'kg', 'FV005', FALSE),

-- Dairy & Eggs
(2, 'Fresh Milk', 'Pure cow milk 1 liter', 85.00, 50, 'liter', 'DE001', TRUE),
(2, 'Eggs', 'Farm fresh eggs', 15.00, 200, 'piece', 'DE002', FALSE),
(2, 'Cheddar Cheese', 'Aged cheddar cheese', 450.00, 30, 'pack', 'DE003', FALSE),
(2, 'Greek Yogurt', 'Creamy Greek yogurt', 180.00, 40, 'cup', 'DE004', FALSE),

-- Meat & Seafood
(3, 'Chicken Breast', 'Boneless chicken breast', 650.00, 25, 'kg', 'MS001', FALSE),
(3, 'Fresh Fish', 'Daily catch fresh fish', 800.00, 15, 'kg', 'MS002', FALSE),

-- Bakery
(4, 'White Bread', 'Fresh white bread loaf', 45.00, 60, 'loaf', 'BK001', FALSE),
(4, 'Croissant', 'Buttery croissant', 25.00, 40, 'piece', 'BK002', FALSE),

-- Pantry Staples
(5, 'Basmati Rice', 'Premium basmati rice 5kg', 750.00, 30, 'pack', 'PS001', TRUE),
(5, 'Cooking Oil', 'Refined cooking oil 1 liter', 180.00, 50, 'liter', 'PS002', FALSE),
(5, 'Wheat Flour', 'All purpose flour 2kg', 120.00, 40, 'pack', 'PS003', FALSE),

-- Beverages
(6, 'Coca Cola', 'Coca Cola 1.5 liter', 120.00, 100, 'bottle', 'BV001', FALSE),
(6, 'Orange Juice', 'Fresh orange juice 1 liter', 200.00, 30, 'bottle', 'BV002', FALSE),
(6, 'Mineral Water', 'Pure mineral water 1 liter', 25.00, 200, 'bottle', 'BV003', FALSE),

-- Snacks
(7, 'Potato Chips', 'Crispy potato chips', 65.00, 80, 'pack', 'SN001', FALSE),
(7, 'Chocolate Cookies', 'Chocolate chip cookies', 85.00, 60, 'pack', 'SN002', FALSE);

-- Insert a test customer
INSERT INTO users (first_name, last_name, email, password, phone, address, city, user_type) VALUES
('John', 'Doe', 'customer@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9841234567', '123 Main Street', 'Kathmandu', 'customer');
