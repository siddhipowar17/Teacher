-- Premium Rental Marketplace Database Schema
CREATE DATABASE IF NOT EXISTS premium_rental;
USE premium_rental;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    role ENUM('user', 'admin') DEFAULT 'user',
    dark_mode TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    price_per_day DECIMAL(10,2) NOT NULL,
    deposit DECIMAL(10,2) DEFAULT 0.00,
    image VARCHAR(255),
    gallery TEXT,
    video VARCHAR(255),
    availability ENUM('available', 'rented', 'maintenance') DEFAULT 'available',
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    total_rentals INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    trending TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    deposit_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'confirmed', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Recently viewed table
CREATE TABLE IF NOT EXISTS recently_viewed (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site statistics table
CREATE TABLE IF NOT EXISTS site_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_key VARCHAR(50) NOT NULL UNIQUE,
    stat_value INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO users (full_name, email, password, role) VALUES
('Admin', 'admin@rental.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert default categories
INSERT INTO categories (name, slug, icon, description) VALUES
('Electronics', 'electronics', 'fa-laptop', 'Latest gadgets and electronics for rent'),
('Cameras', 'cameras', 'fa-camera', 'Professional cameras and photography equipment'),
('Bikes', 'bikes', 'fa-motorcycle', 'Premium bikes and motorcycles'),
('Gaming', 'gaming', 'fa-gamepad', 'Gaming consoles, PCs, and accessories'),
('Furniture', 'furniture', 'fa-couch', 'Modern furniture for homes and events'),
('Fashion', 'fashion', 'fa-tshirt', 'Designer clothing and accessories'),
('Projectors', 'projectors', 'fa-video', 'HD and 4K projectors for events'),
('Speakers', 'speakers', 'fa-volume-up', 'Professional audio equipment'),
('Event Items', 'event-items', 'fa-calendar-star', 'Everything you need for events'),
('Daily Use', 'daily-use', 'fa-box', 'Everyday items available for rent');

-- Insert sample products
INSERT INTO products (category_id, name, slug, description, price_per_day, deposit, availability, rating, total_reviews, total_rentals, featured, trending) VALUES
(1, 'MacBook Pro 16" M3', 'macbook-pro-16-m3', 'Latest MacBook Pro with M3 chip, 32GB RAM, 1TB SSD. Perfect for creative professionals.', 149.99, 500.00, 'available', 4.8, 124, 89, 1, 1),
(1, 'iPad Pro 12.9"', 'ipad-pro-12-9', 'iPad Pro with M2 chip, 256GB, WiFi + Cellular. Includes Apple Pencil.', 79.99, 300.00, 'available', 4.7, 98, 67, 1, 1),
(2, 'Sony A7 IV', 'sony-a7-iv', 'Full-frame mirrorless camera with 33MP sensor. Includes 24-70mm f/2.8 lens.', 129.99, 800.00, 'available', 4.9, 156, 112, 1, 1),
(2, 'Canon EOS R5', 'canon-eos-r5', '45MP full-frame mirrorless. 8K video capable. Body only.', 159.99, 1000.00, 'available', 4.8, 89, 45, 1, 0),
(3, 'Royal Enfield Classic 350', 'royal-enfield-classic-350', 'Iconic motorcycle for road trips. Includes helmet and gear.', 49.99, 200.00, 'available', 4.6, 201, 178, 0, 1),
(3, 'Ducati Monster 821', 'ducati-monster-821', 'Premium Italian superbike. Full insurance included.', 199.99, 1500.00, 'available', 4.9, 67, 34, 1, 1),
(4, 'PlayStation 5 Bundle', 'ps5-bundle', 'PS5 with 2 controllers, headset, and 5 popular games.', 39.99, 200.00, 'available', 4.7, 312, 245, 1, 1),
(4, 'Gaming PC RTX 4090', 'gaming-pc-rtx-4090', 'High-end gaming PC with RTX 4090, i9-13900K, 64GB RAM.', 89.99, 500.00, 'available', 4.9, 78, 56, 1, 0),
(5, 'Herman Miller Aeron Chair', 'herman-miller-aeron', 'Ergonomic office chair. Size B. Fully loaded.', 19.99, 100.00, 'available', 4.5, 156, 134, 0, 1),
(5, 'Standing Desk Uplift V2', 'standing-desk-uplift-v2', 'Electric standing desk with bamboo top. 60x30 inches.', 14.99, 150.00, 'available', 4.4, 89, 67, 0, 0),
(6, 'Designer Tuxedo Set', 'designer-tuxedo-set', 'Premium Italian wool tuxedo. Available in multiple sizes.', 89.99, 300.00, 'available', 4.7, 45, 38, 1, 0),
(7, 'Epson Pro Cinema 4050', 'epson-pro-cinema-4050', '4K PRO-UHD projector with HDR. 2400 lumens. Includes screen.', 69.99, 400.00, 'available', 4.6, 123, 98, 1, 1),
(8, 'JBL PartyBox 710', 'jbl-partybox-710', 'Massive party speaker with built-in lights. 800W output.', 59.99, 250.00, 'available', 4.8, 189, 156, 1, 1),
(9, 'Wedding Decor Package', 'wedding-decor-package', 'Complete wedding decoration set. Includes lights, drapes, centerpieces.', 299.99, 1000.00, 'available', 4.9, 67, 45, 1, 0),
(10, 'Dyson V15 Detect', 'dyson-v15-detect', 'Cordless vacuum cleaner with laser dust detection.', 12.99, 50.00, 'available', 4.5, 234, 198, 0, 1);

-- Insert sample reviews
INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(1, 1, 5, 'Absolutely amazing laptop! Perfect for my video editing project.'),
(1, 3, 5, 'Best camera I have ever used. The image quality is stunning.'),
(1, 7, 4, 'Great gaming setup. My kids loved it during the weekend.');

-- Insert site statistics
INSERT INTO site_stats (stat_key, stat_value) VALUES
('total_users', 12500),
('total_products', 850),
('total_rentals', 45000),
('total_reviews', 8900);
