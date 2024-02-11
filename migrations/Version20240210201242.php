<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210201242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auction (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, start_date DATETIME NOT NULL, current_price DOUBLE PRECISION NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_DEE4F5934584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bid (id INT AUTO_INCREMENT NOT NULL, bidder_id INT NOT NULL, auction_id INT NOT NULL, bid_date DATETIME NOT NULL, value DOUBLE PRECISION NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_4AF2B3F3BE40AFAE (bidder_id), INDEX IDX_4AF2B3F357B8F0DE (auction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F5934584665A FOREIGN KEY (product_id) REFERENCES product_for_sale (id)');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F3BE40AFAE FOREIGN KEY (bidder_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F357B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7597D3FE');
        $this->addSql('DROP INDEX IDX_D34A04AD7597D3FE ON product');
        $this->addSql('ALTER TABLE product CHANGE member_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES member (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD7E3C61F9 ON product (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auction DROP FOREIGN KEY FK_DEE4F5934584665A');
        $this->addSql('ALTER TABLE bid DROP FOREIGN KEY FK_4AF2B3F3BE40AFAE');
        $this->addSql('ALTER TABLE bid DROP FOREIGN KEY FK_4AF2B3F357B8F0DE');
        $this->addSql('DROP TABLE auction');
        $this->addSql('DROP TABLE bid');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7E3C61F9');
        $this->addSql('DROP INDEX IDX_D34A04AD7E3C61F9 ON product');
        $this->addSql('ALTER TABLE product CHANGE owner_id member_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD7597D3FE ON product (member_id)');
    }
}
