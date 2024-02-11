<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208222026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_for_sale CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE product_for_sale ADD CONSTRAINT FK_FD4ECB07BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_for_trade ADD chosen_offer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_for_trade ADD CONSTRAINT FK_900C274134E1CA94 FOREIGN KEY (chosen_offer_id) REFERENCES offer (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_900C274134E1CA94 ON product_for_trade (chosen_offer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_for_sale DROP FOREIGN KEY FK_FD4ECB07BF396750');
        $this->addSql('ALTER TABLE product_for_sale CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE product_for_trade DROP FOREIGN KEY FK_900C274134E1CA94');
        $this->addSql('DROP INDEX UNIQ_900C274134E1CA94 ON product_for_trade');
        $this->addSql('ALTER TABLE product_for_trade DROP chosen_offer_id');
    }
}
