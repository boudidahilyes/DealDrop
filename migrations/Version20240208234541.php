<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208234541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, delivery_order_id INT NOT NULL, delivery_man_id INT DEFAULT NULL, start_time DATETIME DEFAULT NULL, arrival_time DATETIME DEFAULT NULL, state VARCHAR(255) NOT NULL, cost DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_3781EC10ECFE8C54 (delivery_order_id), INDEX IDX_3781EC10FD128646 (delivery_man_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, product_id INT NOT NULL, delivery_adress VARCHAR(255) NOT NULL, order_date DATETIME NOT NULL, INDEX IDX_F52993987597D3FE (member_id), UNIQUE INDEX UNIQ_F52993984584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10ECFE8C54 FOREIGN KEY (delivery_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10FD128646 FOREIGN KEY (delivery_man_id) REFERENCES delivery_man (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC10ECFE8C54');
        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC10FD128646');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987597D3FE');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE `order`');
    }
}
