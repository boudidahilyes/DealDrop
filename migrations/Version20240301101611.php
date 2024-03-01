<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301101611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A');
        $this->addSql('DROP INDEX UNIQ_F52993984584665A ON `order`');
        $this->addSql('ALTER TABLE `order` ADD products_id INT DEFAULT NULL, DROP product_id');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_F52993986C8A81A9 ON `order` (products_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986C8A81A9');
        $this->addSql('DROP INDEX IDX_F52993986C8A81A9 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD product_id INT NOT NULL, DROP products_id');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52993984584665A ON `order` (product_id)');
    }
}
