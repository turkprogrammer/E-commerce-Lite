<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260311194217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL, is_active BOOLEAN NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cart_items AS SELECT id, quantity, cart_id, product_id FROM cart_items');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('CREATE TABLE cart_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantity INTEGER NOT NULL, cart_id INTEGER NOT NULL, product_id INTEGER NOT NULL, CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BEF484454584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cart_items (id, quantity, cart_id, product_id) SELECT id, quantity, cart_id, product_id FROM __temp__cart_items');
        $this->addSql('DROP TABLE __temp__cart_items');
        $this->addSql('CREATE INDEX IDX_BEF484451AD5CDBF ON cart_items (cart_id)');
        $this->addSql('CREATE INDEX IDX_BEF484454584665A ON cart_items (product_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__carts AS SELECT id, session_id, total_amount, total_items FROM carts');
        $this->addSql('DROP TABLE carts');
        $this->addSql('CREATE TABLE carts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_id VARCHAR(64) NOT NULL, total_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, total_items INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO carts (id, session_id, total_amount, total_items) SELECT id, session_id, total_amount, total_items FROM __temp__carts');
        $this->addSql('DROP TABLE __temp__carts');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AAC613FECDF ON carts (session_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__categories AS SELECT id, name, active, parent_id FROM categories');
        $this->addSql('DROP TABLE categories');
        $this->addSql('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, active BOOLEAN DEFAULT 1 NOT NULL, parent_id INTEGER DEFAULT NULL, CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO categories (id, name, active, parent_id) SELECT id, name, active, parent_id FROM __temp__categories');
        $this->addSql('DROP TABLE __temp__categories');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order_items AS SELECT id, product_name, quantity, price, total_price, order_id, product_id FROM order_items');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_name VARCHAR(200) NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION NOT NULL, order_id INTEGER NOT NULL, product_id INTEGER DEFAULT NULL, CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO order_items (id, product_name, quantity, price, total_price, order_id, product_id) SELECT id, product_name, quantity, price, total_price, order_id, product_id FROM __temp__order_items');
        $this->addSql('DROP TABLE __temp__order_items');
        $this->addSql('CREATE INDEX IDX_62809DB04584665A ON order_items (product_id)');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__orders AS SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount FROM orders');
        $this->addSql('DROP TABLE orders');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_number VARCHAR(50) NOT NULL, customer_name VARCHAR(200) NOT NULL, customer_email VARCHAR(200) NOT NULL, customer_phone VARCHAR(50) NOT NULL, delivery_address CLOB NOT NULL, status VARCHAR(50) NOT NULL, total_amount DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO orders (id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount) SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount FROM __temp__orders');
        $this->addSql('DROP TABLE __temp__orders');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE551F0F81 ON orders (order_number)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payments AS SELECT id, method, status, amount, created_at, order_id FROM payments');
        $this->addSql('DROP TABLE payments');
        $this->addSql('CREATE TABLE payments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, method VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, order_id INTEGER NOT NULL, payment_number VARCHAR(50) NOT NULL, updated_at DATETIME NOT NULL, metadata CLOB DEFAULT \'{}\' NOT NULL, CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO payments (id, method, status, amount, created_at, order_id) SELECT id, method, status, amount, created_at, order_id FROM __temp__payments');
        $this->addSql('DROP TABLE __temp__payments');
        $this->addSql('CREATE INDEX IDX_65D29B328D9F6D38 ON payments (order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65D29B32B3A884C2 ON payments (payment_number)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__products AS SELECT id, name, description, sku, price, stock, active, created_at, updated_at, category_id FROM products');
        $this->addSql('DROP TABLE products');
        $this->addSql('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(200) NOT NULL, description CLOB DEFAULT NULL, sku VARCHAR(100) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, stock INTEGER DEFAULT 0 NOT NULL, active BOOLEAN DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INTEGER DEFAULT NULL, CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO products (id, name, description, sku, price, stock, active, created_at, updated_at, category_id) SELECT id, name, description, sku, price, stock, active, created_at, updated_at, category_id FROM __temp__products');
        $this->addSql('DROP TABLE __temp__products');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3BA5A5AF9038C4 ON products (sku)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE cart_items ADD COLUMN added_at DATETIME NOT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__carts AS SELECT id, session_id, total_amount, total_items FROM carts');
        $this->addSql('DROP TABLE carts');
        $this->addSql('CREATE TABLE carts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_id VARCHAR(64) NOT NULL, total_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, total_items INTEGER DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO carts (id, session_id, total_amount, total_items) SELECT id, session_id, total_amount, total_items FROM __temp__carts');
        $this->addSql('DROP TABLE __temp__carts');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AAC613FECDF ON carts (session_id)');
        $this->addSql('ALTER TABLE categories ADD COLUMN description CLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE categories ADD COLUMN slug VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE categories ADD COLUMN sort_order INTEGER DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE order_items ADD COLUMN meta CLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN notes CLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN currency VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN shipping_cost DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN discount_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN confirmed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN shipped_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN delivered_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN cancelled_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payments AS SELECT id, amount, status, method, created_at, order_id FROM payments');
        $this->addSql('DROP TABLE payments');
        $this->addSql('CREATE TABLE payments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(50) NOT NULL, method VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, order_id INTEGER DEFAULT NULL, transaction_id VARCHAR(255) NOT NULL, currency VARCHAR(3) NOT NULL, description CLOB DEFAULT NULL, error_message CLOB DEFAULT NULL, paid_at DATETIME DEFAULT NULL, meta CLOB DEFAULT NULL, processed_at DATETIME DEFAULT NULL, failed_at DATETIME DEFAULT NULL, refunded_at DATETIME DEFAULT NULL, CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO payments (id, amount, status, method, created_at, order_id) SELECT id, amount, status, method, created_at, order_id FROM __temp__payments');
        $this->addSql('DROP TABLE __temp__payments');
        $this->addSql('CREATE INDEX IDX_65D29B328D9F6D38 ON payments (order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65D29B322FC0CB0F ON payments (transaction_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__products AS SELECT id, name, price, stock, active, created_at, updated_at, description, sku, category_id FROM products');
        $this->addSql('DROP TABLE products');
        $this->addSql('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(200) NOT NULL, price DOUBLE PRECISION NOT NULL, stock INTEGER DEFAULT 0 NOT NULL, active BOOLEAN DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, description CLOB DEFAULT NULL, sku VARCHAR(200) DEFAULT NULL, category_id INTEGER DEFAULT NULL, slug VARCHAR(100) DEFAULT NULL, old_price DOUBLE PRECISION DEFAULT NULL, featured BOOLEAN DEFAULT 0 NOT NULL, image_url VARCHAR(255) DEFAULT NULL, images CLOB DEFAULT NULL, meta CLOB DEFAULT NULL, CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO products (id, name, price, stock, active, created_at, updated_at, description, sku, category_id) SELECT id, name, price, stock, active, created_at, updated_at, description, sku, category_id FROM __temp__products');
        $this->addSql('DROP TABLE __temp__products');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
    }
}
