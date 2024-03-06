<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306163818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reminder (id INT AUTO_INCREMENT NOT NULL, auction_id INT NOT NULL, status VARCHAR(255) NOT NULL, reminder_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_40374F4057B8F0DE (auction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reminder_member (reminder_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_BBC7EC90D987BE75 (reminder_id), INDEX IDX_BBC7EC907597D3FE (member_id), PRIMARY KEY(reminder_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F4057B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id)');
        $this->addSql('ALTER TABLE reminder_member ADD CONSTRAINT FK_BBC7EC90D987BE75 FOREIGN KEY (reminder_id) REFERENCES reminder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reminder_member ADD CONSTRAINT FK_BBC7EC907597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY FK_40374F4057B8F0DE');
        $this->addSql('ALTER TABLE reminder_member DROP FOREIGN KEY FK_BBC7EC90D987BE75');
        $this->addSql('ALTER TABLE reminder_member DROP FOREIGN KEY FK_BBC7EC907597D3FE');
        $this->addSql('DROP TABLE reminder');
        $this->addSql('DROP TABLE reminder_member');
    }
}
