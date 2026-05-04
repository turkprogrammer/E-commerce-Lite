<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260306085947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, added_at DATETIME NOT NULL, cart_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BEF484454584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BEF484451AD5CDBF ON cart_items (cart_id)');
        $this->addSql('CREATE INDEX IDX_BEF484454584665A ON cart_items (product_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_cart_product ON cart_items (cart_id, product_id)');
        $this->addSql('CREATE TABLE carts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_id VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, total_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, total_items INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AAC613FECDF ON carts (session_id)');
        $this->addSql('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description CLOB DEFAULT NULL, slug VARCHAR(100) DEFAULT NULL, active BOOLEAN DEFAULT 1 NOT NULL, sort_order INTEGER DEFAULT 0 NOT NULL, parent_id INTEGER DEFAULT NULL, CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_name VARCHAR(200) NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION DEFAULT 0 NOT NULL, meta CLOB DEFAULT NULL, order_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('CREATE INDEX IDX_62809DB04584665A ON order_items (product_id)');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_number VARCHAR(20) NOT NULL, customer_name VARCHAR(50) NOT NULL, customer_email VARCHAR(255) NOT NULL, customer_phone VARCHAR(20) DEFAULT NULL, delivery_address CLOB DEFAULT NULL, notes CLOB DEFAULT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, total_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, currency VARCHAR(10) DEFAULT \'RUB\' NOT NULL, shipping_cost DOUBLE PRECISION DEFAULT 0 NOT NULL, discount_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, meta CLOB DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, confirmed_at DATETIME DEFAULT NULL, shipped_at DATETIME DEFAULT NULL, delivered_at DATETIME DEFAULT NULL, cancelled_at DATETIME DEFAULT NULL, cart_id INTEGER DEFAULT NULL, CONSTRAINT FK_E52FFDEE1AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE551F0F81 ON orders (order_number)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE1AD5CDBF ON orders (cart_id)');
        $this->addSql('CREATE TABLE payments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, transaction_id VARCHAR(50) NOT NULL, method VARCHAR(50) DEFAULT \'card\' NOT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(10) DEFAULT \'RUB\' NOT NULL, description CLOB DEFAULT NULL, meta CLOB DEFAULT NULL, error_message CLOB DEFAULT NULL, created_at DATETIME NOT NULL, processed_at DATETIME DEFAULT NULL, paid_at DATETIME DEFAULT NULL, failed_at DATETIME DEFAULT NULL, refunded_at DATETIME DEFAULT NULL, order_id INTEGER DEFAULT NULL, CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65D29B322FC0CB0F ON payments (transaction_id)');
        $this->addSql('CREATE INDEX IDX_65D29B328D9F6D38 ON payments (order_id)');
        $this->addSql('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(200) NOT NULL, description CLOB DEFAULT NULL, sku VARCHAR(200) DEFAULT NULL, slug VARCHAR(100) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, old_price DOUBLE PRECISION DEFAULT NULL, stock INTEGER DEFAULT 0 NOT NULL, active BOOLEAN DEFAULT 1 NOT NULL, featured BOOLEAN DEFAULT 0 NOT NULL, image_url VARCHAR(255) DEFAULT NULL, images CLOB DEFAULT NULL, meta CLOB DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, category_id INTEGER DEFAULT NULL, CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('DROP TABLE carts');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE products');
    }
}
