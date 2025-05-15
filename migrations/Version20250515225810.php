<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515225810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
        CREATE TABLE IF NOT EXISTS `order` (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, cart_id INT DEFAULT NULL, date DATE NOT NULL, quantity INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_F52993984584665A (product_id), INDEX IDX_F52993981AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    SQL);
        $this->addSql(<<<'SQL'
        CREATE TABLE IF NOT EXISTS user (id INT AUTO_INCREMENT NOT NULL, cart_id INT DEFAULT NULL, wishlist_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL, username VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_8D93D6491AD5CDBF (cart_id), UNIQUE INDEX UNIQ_8D93D649FB8E54CD (wishlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    SQL);
        $this->addSql(<<<'SQL'
        CREATE TABLE IF NOT EXISTS wish (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, wishlist_id INT NOT NULL, INDEX IDX_D7D174C94584665A (product_id), INDEX IDX_D7D174C9FB8E54CD (wishlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    SQL);
        $this->addSql(<<<'SQL'
        CREATE TABLE IF NOT EXISTS wishlist (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    SQL);
        $this->addSql(<<<'SQL'
        ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)
    SQL);
        $this->addSql(<<<'SQL'
        ALTER TABLE `order` ADD CONSTRAINT FK_F52993981AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)
    SQL);
        $this->addSql(<<<'SQL'
        ALTER TABLE user ADD CONSTRAINT FK_8D93D6491AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)
    SQL);
        $this->addSql(<<<'SQL'
        ALTER TABLE user ADD CONSTRAINT FK_8D93D649FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)
    SQL);
        $this->addSql(<<<'SQL'
        ALTER TABLE wish ADD CONSTRAINT FK_D7D174C94584665A FOREIGN KEY (product_id) REFERENCES product (id)
    SQL);
        $this->addSql(<<<'SQL'
        ALTER TABLE wish ADD CONSTRAINT FK_D7D174C9FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)
    SQL);
    }
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F52993981AD5CDBF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491AD5CDBF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FB8E54CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish DROP FOREIGN KEY FK_D7D174C94584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish DROP FOREIGN KEY FK_D7D174C9FB8E54CD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `order`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wish
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wishlist
        SQL);
    }
}
