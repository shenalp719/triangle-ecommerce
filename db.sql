-- Triangle Printing Solutions Database Schema
-- Created: 2026-02-20
-- Compatible with: MySQL 5.7+

-- ============================================
-- Create Database
-- ============================================
CREATE DATABASE IF NOT EXISTS triangle_ecommerce;
USE triangle_ecommerce;

-- ============================================
-- Table: users
-- Description: Customer and admin user accounts
-- ============================================
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  phone VARCHAR(20),
  company VARCHAR(100),
  address VARCHAR(255),
  city VARCHAR(100),
  state VARCHAR(50),
  postal_code VARCHAR(20),
  country VARCHAR(100),
  role ENUM('customer', 'admin') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: products
-- Description: Product catalog (frames, mugs, shirts, caps)
-- ============================================
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(100) NOT NULL,
  base_price DECIMAL(10, 2) NOT NULL,
  image MEDIUMBLOB,
  specifications JSON,
  available TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_available (available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: frames
-- Description: Frame styles and dimensions for customizer
-- ============================================
CREATE TABLE IF NOT EXISTS frames (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  style VARCHAR(100) NOT NULL,
  dimensions JSON,
  price_multiplier DECIMAL(5, 2) DEFAULT 1.0,
  thumbnail MEDIUMBLOB,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_style (style)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: designs
-- Description: User-created custom designs (frames, mugs, etc)
-- ============================================
CREATE TABLE IF NOT EXISTS designs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT,
  name VARCHAR(255) NOT NULL,
  canvas_json LONGTEXT,
  preview_image LONGBLOB,
  width INT,
  height INT,
  resolution_dpi INT DEFAULT 300,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
  INDEX idx_user_id (user_id),
  INDEX idx_product_id (product_id),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: orders
-- Description: Customer orders and order status
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_number VARCHAR(50) UNIQUE NOT NULL,
  status ENUM('pending', 'processing', 'printing', 'prepared', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  total_amount DECIMAL(10, 2) NOT NULL,
  delivery_address TEXT,
  delivery_notes TEXT,
  tracking_number VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_order_number (order_number),
  INDEX idx_status (status),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: order_items
-- Description: Individual items within each order
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT,
  design_id INT,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10, 2) NOT NULL,
  print_file LONGBLOB,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
  FOREIGN KEY (design_id) REFERENCES designs(id) ON DELETE SET NULL,
  INDEX idx_order_id (order_id),
  INDEX idx_product_id (product_id),
  INDEX idx_design_id (design_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data (Optional - Remove if not needed)
-- ============================================

-- Insert sample products
INSERT INTO products (name, description, category, base_price, specifications, available) VALUES
('8x10 Poster Frame', 'Premium poster frame 8x10 inches', 'Frames', 25.00, '{"dimensions":"8x10","material":"metal"}', 1),
('11x14 Poster Frame', 'Large poster frame 11x14 inches', 'Frames', 45.00, '{"dimensions":"11x14","material":"metal"}', 1),
('11oz Coffee Mug', 'Standard ceramic coffee mug 11oz', 'Drinkware', 12.00, '{"capacity":"11oz","material":"ceramic"}', 1),
('15oz Coffee Mug', 'Large ceramic coffee mug 15oz', 'Drinkware', 15.00, '{"capacity":"15oz","material":"ceramic"}', 1),
('Unisex T-Shirt', 'Premium cotton unisex t-shirt', 'Apparel', 18.00, '{"material":"100% cotton","sizes":"XS-3XL"}', 1),
('Premium T-Shirt', 'High-quality premium t-shirt', 'Apparel', 22.00, '{"material":"100% organic cotton","sizes":"XS-3XL"}', 1),
('Baseball Cap', 'Adjustable baseball cap', 'Headwear', 15.00, '{"material":"cotton","adjustment":"adjustable"}', 1),
('Premium Cap', 'Premium adjustable cap', 'Headwear', 18.00, '{"material":"premium cotton","adjustment":"adjustable"}', 1);

-- Insert sample frames
INSERT INTO frames (name, style, dimensions, price_multiplier, thumbnail) VALUES
('Modern Minimal', 'minimal', '{"width":8,"height":10,"frameWidth":0.5}', 1.0, NULL),
('Classic Wood', 'wood', '{"width":8,"height":10,"frameWidth":2}', 1.2, NULL),
('Gold Elegant', 'gold', '{"width":8,"height":10,"frameWidth":1.5}', 1.3, NULL),
('White Clean', 'white', '{"width":8,"height":10,"frameWidth":1}', 1.1, NULL);

-- Insert sample demo admin account (password: admin123456)
-- To use this, uncomment the line below and update the password hash
-- INSERT INTO users (email, password, first_name, last_name, role) 
-- VALUES ('admin@triangleprinting.com', '$2y$10$YourHashedPasswordHere', 'Admin', 'User', 'admin');

-- Insert sample demo customer account (password: demo123456)
-- To use this, uncomment the line below and update the password hash
-- INSERT INTO users (email, password, first_name, last_name, phone, address, city, state, postal_code, country, role) 
-- VALUES ('demo@triangleprinting.com', '$2y$10$YourHashedPasswordHere', 'Demo', 'Customer', '555-1234', '123 Main St', 'New York', 'NY', '10001', 'USA', 'customer');

-- ============================================
-- Database Setup Complete
-- ============================================
-- All tables have been created successfully!
-- You can now use the application at:
-- http://localhost/triangle-ecommerce/
--
-- Note: To create user accounts with proper bcrypt hashing,
-- use the registration page at: /register.php
