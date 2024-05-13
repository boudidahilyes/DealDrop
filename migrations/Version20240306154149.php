<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306154149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reminder_member (reminder_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_BBC7EC90D987BE75 (reminder_id), INDEX IDX_BBC7EC907597D3FE (member_id), PRIMARY KEY(reminder_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reminder_member ADD CONSTRAINT FK_BBC7EC90D987BE75 FOREIGN KEY (reminder_id) REFERENCES reminder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reminder_member ADD CONSTRAINT FK_BBC7EC907597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reminder DROP INDEX IDX_40374F4057B8F0DE, ADD UNIQUE INDEX UNIQ_40374F4057B8F0DE (auction_id)');
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY FK_40374F407E3C61F9');
        $this->addSql('DROP INDEX IDX_40374F407E3C61F9 ON reminder');
        $this->addSql('ALTER TABLE reminder ADD reminder_date DATETIME NOT NULL, DROP owner_id, DROP reminder_time, CHANGE auction_id auction_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reminder_member DROP FOREIGN KEY FK_BBC7EC90D987BE75');
        $this->addSql('ALTER TABLE reminder_member DROP FOREIGN KEY FK_BBC7EC907597D3FE');
        $this->addSql('DROP TABLE reminder_member');
        $this->addSql('ALTER TABLE reminder DROP INDEX UNIQ_40374F4057B8F0DE, ADD INDEX IDX_40374F4057B8F0DE (auction_id)');
        $this->addSql('ALTER TABLE reminder ADD owner_id INT DEFAULT NULL, ADD reminder_time DATETIME DEFAULT NULL, DROP reminder_date, CHANGE auction_id auction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F407E3C61F9 FOREIGN KEY (owner_id) REFERENCES member (id)');
        $this->addSql('CREATE INDEX IDX_40374F407E3C61F9 ON reminder (owner_id)');
    }
}
