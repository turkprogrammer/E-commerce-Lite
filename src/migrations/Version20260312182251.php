<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260312182251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__carts AS SELECT id, session_id, total_amount, total_items FROM carts');
        $this->addSql('DROP TABLE carts');
        $this->addSql('CREATE TABLE carts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_id VARCHAR(64) NOT NULL, total_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, total_items INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO carts (id, session_id, total_amount, total_items) SELECT id, session_id, total_amount, total_items FROM __temp__carts');
        $this->addSql('DROP TABLE __temp__carts');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AAC613FECDF ON carts (session_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__orders AS SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount, created_at, updated_at FROM orders');
        $this->addSql('DROP TABLE orders');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_number VARCHAR(50) NOT NULL, customer_name VARCHAR(200) NOT NULL, customer_email VARCHAR(200) NOT NULL, customer_phone VARCHAR(50) NOT NULL, delivery_address CLOB NOT NULL, status VARCHAR(50) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO orders (id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount, created_at, updated_at) SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount, created_at, updated_at FROM __temp__orders');
        $this->addSql('DROP TABLE __temp__orders');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE551F0F81 ON orders (order_number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__carts AS SELECT id, session_id, total_amount, total_items FROM carts');
        $this->addSql('DROP TABLE carts');
        $this->addSql('CREATE TABLE carts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_id VARCHAR(64) NOT NULL, total_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, total_items INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO carts (id, session_id, total_amount, total_items) SELECT id, session_id, total_amount, total_items FROM __temp__carts');
        $this->addSql('DROP TABLE __temp__carts');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AAC613FECDF ON carts (session_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__orders AS SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount, created_at, updated_at FROM orders');
        $this->addSql('DROP TABLE orders');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_number VARCHAR(50) NOT NULL, customer_name VARCHAR(200) NOT NULL, customer_email VARCHAR(200) NOT NULL, customer_phone VARCHAR(50) NOT NULL, delivery_address CLOB NOT NULL, status VARCHAR(50) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO orders (id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount, created_at, updated_at) SELECT id, order_number, customer_name, customer_email, customer_phone, delivery_address, status, total_amount, created_at, updated_at FROM __temp__orders');
        $this->addSql('DROP TABLE __temp__orders');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE551F0F81 ON orders (order_number)');
    }
}
