<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260306122449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__carts AS SELECT id, session_id, created_at, updated_at, expires_at, total_amount, total_items FROM carts');
        $this->addSql('DROP TABLE carts');
        $this->addSql('CREATE TABLE carts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_id VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, total_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, total_items INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO carts (id, session_id, created_at, updated_at, expires_at, total_amount, total_items) SELECT id, session_id, created_at, updated_at, expires_at, total_amount, total_items FROM __temp__carts');
        $this->addSql('DROP TABLE __temp__carts');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AAC613FECDF ON carts (session_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order_items AS SELECT id, product_name, quantity, price, total_price, meta, order_id, product_id FROM order_items');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_name VARCHAR(200) NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION DEFAULT 0 NOT NULL, meta CLOB DEFAULT NULL, order_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO order_items (id, product_name, quantity, price, total_price, meta, order_id, product_id) SELECT id, product_name, quantity, price, total_price, meta, order_id, product_id FROM __temp__order_items');
        $this->addSql('DROP TABLE __temp__order_items');
        $this->addSql('CREATE INDEX IDX_62809DB04584665A ON order_items (product_id)');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__orders AS SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, notes, status, total_amount, currency, shipping_cost, discount_amount, created_at, updated_at, cart_id FROM orders');
        $this->addSql('DROP TABLE orders');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_number VARCHAR(20) NOT NULL, customer_name VARCHAR(50) NOT NULL, customer_email VARCHAR(255) NOT NULL, customer_phone VARCHAR(20) DEFAULT NULL, delivery_address CLOB DEFAULT NULL, notes CLOB DEFAULT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, total_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, currency VARCHAR(10) DEFAULT \'RUB\' NOT NULL, shipping_cost DOUBLE PRECISION DEFAULT 0 NOT NULL, discount_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, cart_id INTEGER DEFAULT NULL, CONSTRAINT FK_E52FFDEE1AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO orders (id, order_number, customer_name, customer_email, customer_phone, delivery_address, notes, status, total_amount, currency, shipping_cost, discount_amount, created_at, updated_at, cart_id) SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, notes, status, total_amount, currency, shipping_cost, discount_amount, created_at, updated_at, cart_id FROM __temp__orders');
        $this->addSql('DROP TABLE __temp__orders');
        $this->addSql('CREATE INDEX IDX_E52FFDEE1AD5CDBF ON orders (cart_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE551F0F81 ON orders (order_number)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payments AS SELECT id, transaction_id, method, status, amount, currency, description, error_message, created_at, paid_at, order_id FROM payments');
        $this->addSql('DROP TABLE payments');
        $this->addSql('CREATE TABLE payments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, transaction_id VARCHAR(50) NOT NULL, method VARCHAR(50) DEFAULT \'card\' NOT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(10) DEFAULT \'RUB\' NOT NULL, description CLOB DEFAULT NULL, error_message CLOB DEFAULT NULL, created_at DATETIME NOT NULL, paid_at DATETIME DEFAULT NULL, order_id INTEGER DEFAULT NULL, CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO payments (id, transaction_id, method, status, amount, currency, description, error_message, created_at, paid_at, order_id) SELECT id, transaction_id, method, status, amount, currency, description, error_message, created_at, paid_at, order_id FROM __temp__payments');
        $this->addSql('DROP TABLE __temp__payments');
        $this->addSql('CREATE INDEX IDX_65D29B328D9F6D38 ON payments (order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65D29B322FC0CB0F ON payments (transaction_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__carts AS SELECT id, session_id, created_at, updated_at, expires_at, total_amount, total_items FROM carts');
        $this->addSql('DROP TABLE carts');
        $this->addSql('CREATE TABLE carts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_id VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, total_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, total_items INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO carts (id, session_id, created_at, updated_at, expires_at, total_amount, total_items) SELECT id, session_id, created_at, updated_at, expires_at, total_amount, total_items FROM __temp__carts');
        $this->addSql('DROP TABLE __temp__carts');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AAC613FECDF ON carts (session_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order_items AS SELECT id, product_name, quantity, price, total_price, meta, order_id, product_id FROM order_items');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_name VARCHAR(200) NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION DEFAULT \'0\' NOT NULL, meta CLOB DEFAULT NULL, order_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO order_items (id, product_name, quantity, price, total_price, meta, order_id, product_id) SELECT id, product_name, quantity, price, total_price, meta, order_id, product_id FROM __temp__order_items');
        $this->addSql('DROP TABLE __temp__order_items');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('CREATE INDEX IDX_62809DB04584665A ON order_items (product_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__orders AS SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, notes, status, total_amount, currency, shipping_cost, discount_amount, created_at, updated_at, cart_id FROM orders');
        $this->addSql('DROP TABLE orders');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_number VARCHAR(20) NOT NULL, customer_name VARCHAR(50) NOT NULL, customer_email VARCHAR(255) NOT NULL, customer_phone VARCHAR(20) DEFAULT NULL, delivery_address CLOB DEFAULT NULL, notes CLOB DEFAULT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, total_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, currency VARCHAR(10) DEFAULT \'RUB\' NOT NULL, shipping_cost DOUBLE PRECISION DEFAULT \'0\' NOT NULL, discount_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, cart_id INTEGER DEFAULT NULL, meta CLOB DEFAULT NULL, confirmed_at DATETIME DEFAULT NULL, shipped_at DATETIME DEFAULT NULL, delivered_at DATETIME DEFAULT NULL, cancelled_at DATETIME DEFAULT NULL, CONSTRAINT FK_E52FFDEE1AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO orders (id, order_number, customer_name, customer_email, customer_phone, delivery_address, notes, status, total_amount, currency, shipping_cost, discount_amount, created_at, updated_at, cart_id) SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, notes, status, total_amount, currency, shipping_cost, discount_amount, created_at, updated_at, cart_id FROM __temp__orders');
        $this->addSql('DROP TABLE __temp__orders');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE551F0F81 ON orders (order_number)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE1AD5CDBF ON orders (cart_id)');
        $this->addSql('ALTER TABLE payments ADD COLUMN meta CLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE payments ADD COLUMN processed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE payments ADD COLUMN failed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE payments ADD COLUMN refunded_at DATETIME DEFAULT NULL');
    }
}
