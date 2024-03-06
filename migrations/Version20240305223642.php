<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305223642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reminder (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, auction_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, reminder_time DATETIME DEFAULT NULL, INDEX IDX_40374F407E3C61F9 (owner_id), INDEX IDX_40374F4057B8F0DE (auction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F407E3C61F9 FOREIGN KEY (owner_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F4057B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id)');
        $this->addSql('ALTER TABLE auction DROP FOREIGN KEY FK_DEE4F5934584665A');
        $this->addSql('DROP INDEX UNIQ_DEE4F5934584665A ON auction');
        $this->addSql('ALTER TABLE auction ADD highest_bid_id INT DEFAULT NULL, DROP product_id, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F593AEB93B9F FOREIGN KEY (highest_bid_id) REFERENCES bid (id)');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F593BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DEE4F593AEB93B9F ON auction (highest_bid_id)');
        $this->addSql('ALTER TABLE bid CHANGE bidder_id bidder_id INT DEFAULT NULL, CHANGE bid_date bid_date DATETIME DEFAULT NULL, CHANGE state state VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY FK_40374F407E3C61F9');
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY FK_40374F4057B8F0DE');
        $this->addSql('DROP TABLE reminder');
        $this->addSql('ALTER TABLE auction DROP FOREIGN KEY FK_DEE4F593AEB93B9F');
        $this->addSql('ALTER TABLE auction DROP FOREIGN KEY FK_DEE4F593BF396750');
        $this->addSql('DROP INDEX UNIQ_DEE4F593AEB93B9F ON auction');
        $this->addSql('ALTER TABLE auction ADD product_id INT NOT NULL, DROP highest_bid_id, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F5934584665A FOREIGN KEY (product_id) REFERENCES product_for_sale (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DEE4F5934584665A ON auction (product_id)');
        $this->addSql('ALTER TABLE bid CHANGE bidder_id bidder_id INT NOT NULL, CHANGE bid_date bid_date DATETIME NOT NULL, CHANGE state state VARCHAR(255) NOT NULL');
    }
}
