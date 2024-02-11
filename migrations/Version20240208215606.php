<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208215606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, product_posted_id INT NOT NULL, product_offered_id INT NOT NULL, INDEX IDX_29D6873E4064D2CB (product_posted_id), UNIQUE INDEX UNIQ_29D6873E8A0E7271 (product_offered_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E4064D2CB FOREIGN KEY (product_posted_id) REFERENCES product_for_trade (id)');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E8A0E7271 FOREIGN KEY (product_offered_id) REFERENCES product_for_trade (id)');
        $this->addSql('ALTER TABLE product ADD member_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD7597D3FE ON product (member_id)');
        $this->addSql('ALTER TABLE product_for_rent DROP negotiable, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE product_for_rent ADD CONSTRAINT FK_1A7D46CEBF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_for_trade ADD trade_type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E4064D2CB');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E8A0E7271');
        $this->addSql('DROP TABLE offer');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7597D3FE');
        $this->addSql('DROP INDEX IDX_D34A04AD7597D3FE ON product');
        $this->addSql('ALTER TABLE product DROP member_id');
        $this->addSql('ALTER TABLE product_for_rent DROP FOREIGN KEY FK_1A7D46CEBF396750');
        $this->addSql('ALTER TABLE product_for_rent ADD negotiable TINYINT(1) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE product_for_trade DROP trade_type');
    }
}
