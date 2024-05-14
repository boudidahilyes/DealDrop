<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240513201624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bid DROP isHighest');
        $this->addSql('ALTER TABLE `order` ADD price_added DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY fk_member_id');
        $this->addSql('DROP INDEX fk_member_id ON reminder');
        $this->addSql('ALTER TABLE reminder DROP member_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bid ADD isHighest TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` DROP price_added');
        $this->addSql('ALTER TABLE reminder ADD member_id INT NOT NULL');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT fk_member_id FOREIGN KEY (member_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX fk_member_id ON reminder (member_id)');
    }
}
