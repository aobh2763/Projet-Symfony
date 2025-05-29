<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515171947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE accessory (id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ammo (id INT NOT NULL, gun_id INT DEFAULT NULL, quantity INT NOT NULL, UNIQUE INDEX UNIQ_EF8F7E2AD7F86DEC (gun_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, prixtotal DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE gun (id INT NOT NULL, accuracy DOUBLE PRECISION NOT NULL, caliber DOUBLE PRECISION NOT NULL, gun_range DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE melee (id INT NOT NULL, reach DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `orders` (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, cart_id INT DEFAULT NULL, date DATETIME NOT NULL, quantity INT NOT NULL, status VARCHAR(127) NOT NULL, INDEX IDX_E52FFDEE4584665A (product_id), INDEX IDX_E52FFDEE1AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(2047) NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(1023) NOT NULL, price DOUBLE PRECISION NOT NULL, rating DOUBLE PRECISION NOT NULL, sale DOUBLE PRECISION DEFAULT NULL, stock INT NOT NULL, weight DOUBLE PRECISION NOT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wish (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accessory ADD CONSTRAINT FK_A1B1251CBF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ammo ADD CONSTRAINT FK_EF8F7E2AD7F86DEC FOREIGN KEY (gun_id) REFERENCES gun (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ammo ADD CONSTRAINT FK_EF8F7E2ABF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gun ADD CONSTRAINT FK_4A9BC55BBF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE melee ADD CONSTRAINT FK_CFE77BA1BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` ADD CONSTRAINT FK_E52FFDEE4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` ADD CONSTRAINT FK_E52FFDEE1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE accessory DROP FOREIGN KEY FK_A1B1251CBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ammo DROP FOREIGN KEY FK_EF8F7E2AD7F86DEC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ammo DROP FOREIGN KEY FK_EF8F7E2ABF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gun DROP FOREIGN KEY FK_4A9BC55BBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE melee DROP FOREIGN KEY FK_CFE77BA1BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` DROP FOREIGN KEY FK_E52FFDEE4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` DROP FOREIGN KEY FK_E52FFDEE1AD5CDBF
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE accessory
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ammo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE gun
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE melee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `orders`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wish
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wishlist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
