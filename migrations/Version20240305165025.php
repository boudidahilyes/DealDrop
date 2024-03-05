<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305165025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auction ADD highest_bid_id INT DEFAULT NULL, DROP highest_bid');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F593AEB93B9F FOREIGN KEY (highest_bid_id) REFERENCES bid (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DEE4F593AEB93B9F ON auction (highest_bid_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auction DROP FOREIGN KEY FK_DEE4F593AEB93B9F');
        $this->addSql('DROP INDEX UNIQ_DEE4F593AEB93B9F ON auction');
        $this->addSql('ALTER TABLE auction ADD highest_bid VARCHAR(255) DEFAULT NULL, DROP highest_bid_id');
    }
}
