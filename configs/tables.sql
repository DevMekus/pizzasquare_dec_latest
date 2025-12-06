-- categories


-- product_stock for product-level inventory
CREATE TABLE product_stock (
id INT AUTO_INCREMENT PRIMARY KEY,
product_id INT NOT NULL,
size_id INT NOT NULL,
qty INT NOT NULL DEFAULT 0,
low_stock_threshold INT DEFAULT 2,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
UNIQUE(product_id, size_id),
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
);


-- orders
CREATE TABLE orders (
id BIGINT AUTO_INCREMENT PRIMARY KEY,
customer_name VARCHAR(255) NULL,
customer_phone VARCHAR(50) NULL,
status ENUM('pending','paid','preparing','completed','cancelled') DEFAULT 'pending',
total DECIMAL(12,2) NOT NULL DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- order items
CREATE TABLE order_items (
id BIGINT AUTO_INCREMENT PRIMARY KEY,
order_id BIGINT NOT NULL,
product_id INT NOT NULL,
size_id INT NOT NULL,
unit_price DECIMAL(10,2) NOT NULL,
qty INT NOT NULL,
subtotal DECIMAL(12,2) NOT NULL,
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
FOREIGN KEY (product_id) REFERENCES products(id)
);


-- payments: one or many rows per order (supports split)
CREATE TABLE payments (
id BIGINT AUTO_INCREMENT PRIMARY KEY,
order_id BIGINT NOT NULL,
method ENUM('cash','card','transfer') NOT NULL,
amount DECIMAL(12,2) NOT NULL,
reference VARCHAR(255) NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);


-- audit log for stock movements
CREATE TABLE stock_movements (
id BIGINT AUTO_INCREMENT PRIMARY KEY,
movement_type ENUM('sale','manual_adjust','restock') NOT NULL,
reference_type ENUM('category_size','product_size') NOT NULL,
reference_id INT NOT NULL,
qty_change INT NOT NULL,
note TEXT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);