<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515205923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE wish ADD product_id INT NOT NULL, ADD wishlist_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish ADD CONSTRAINT FK_D7D174C94584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish ADD CONSTRAINT FK_D7D174C9FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D7D174C94584665A ON wish (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D7D174C9FB8E54CD ON wish (wishlist_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE wish DROP FOREIGN KEY FK_D7D174C94584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish DROP FOREIGN KEY FK_D7D174C9FB8E54CD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D7D174C94584665A ON wish
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D7D174C9FB8E54CD ON wish
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish DROP product_id, DROP wishlist_id
        SQL);
    }
}
