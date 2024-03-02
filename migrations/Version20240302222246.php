<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302222246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, role VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auction (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, start_date DATETIME NOT NULL, current_price DOUBLE PRECISION NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_DEE4F5934584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bid (id INT AUTO_INCREMENT NOT NULL, bidder_id INT NOT NULL, auction_id INT NOT NULL, bid_date DATETIME NOT NULL, value DOUBLE PRECISION NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_4AF2B3F3BE40AFAE (bidder_id), INDEX IDX_4AF2B3F357B8F0DE (auction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, delivery_order_id INT NOT NULL, delivery_man_id INT DEFAULT NULL, start_time DATETIME DEFAULT NULL, arrival_time DATETIME DEFAULT NULL, state VARCHAR(255) NOT NULL, coordinates VARCHAR(255) NOT NULL, current_coordinates VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3781EC10ECFE8C54 (delivery_order_id), INDEX IDX_3781EC10FD128646 (delivery_man_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery_man (id INT NOT NULL, disponibility VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, area LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, date DATETIME NOT NULL, description LONGTEXT NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_3BAE0AA7876C4DDA (organizer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, member_id INT NOT NULL, rating DOUBLE PRECISION NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_D229445871F7E88B (event_id), INDEX IDX_D22944587597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, delivery_man_id INT DEFAULT NULL, product_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, modify_date DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_C53D045FFD128646 (delivery_man_id), INDEX IDX_C53D045F4584665A (product_id), INDEX IDX_C53D045FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, product_posted_id INT NOT NULL, product_offered_id INT NOT NULL, INDEX IDX_29D6873E4064D2CB (product_posted_id), UNIQUE INDEX UNIQ_29D6873E8A0E7271 (product_offered_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, products_id INT DEFAULT NULL, delivery_adress VARCHAR(255) NOT NULL, order_date DATETIME NOT NULL, rent_days INT DEFAULT NULL, payment VARCHAR(255) DEFAULT NULL, INDEX IDX_F52993987597D3FE (member_id), INDEX IDX_F52993986C8A81A9 (products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, product_category_id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, add_date DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_D34A04ADBE6903FD (product_category_id), INDEX IDX_D34A04AD7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_for_rent (id INT NOT NULL, price_per_day DOUBLE PRECISION NOT NULL, disponibility VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_for_sale (id INT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_for_trade (id INT NOT NULL, chosen_offer_id INT DEFAULT NULL, trade_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_900C274134E1CA94 (chosen_offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, support_ticket_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, add_date DATETIME NOT NULL, INDEX IDX_3E7B0BFBC6D2DC64 (support_ticket_id), INDEX IDX_3E7B0BFBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE support_ticket (id INT AUTO_INCREMENT NOT NULL, support_ticket_category_id INT NOT NULL, user_id INT NOT NULL, subject VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_1F5A4D53C78D6829 (support_ticket_category_id), INDEX IDX_1F5A4D53A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, cin INT NOT NULL, adress VARCHAR(255) NOT NULL, phone INT NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F5934584665A FOREIGN KEY (product_id) REFERENCES product_for_sale (id)');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F3BE40AFAE FOREIGN KEY (bidder_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F357B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10ECFE8C54 FOREIGN KEY (delivery_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10FD128646 FOREIGN KEY (delivery_man_id) REFERENCES delivery_man (id)');
        $this->addSql('ALTER TABLE delivery_man ADD CONSTRAINT FK_4E0DFC7CBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7876C4DDA FOREIGN KEY (organizer_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D229445871F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944587597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FFD128646 FOREIGN KEY (delivery_man_id) REFERENCES delivery_man (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E4064D2CB FOREIGN KEY (product_posted_id) REFERENCES product_for_trade (id)');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E8A0E7271 FOREIGN KEY (product_offered_id) REFERENCES product_for_trade (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBE6903FD FOREIGN KEY (product_category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE product_for_rent ADD CONSTRAINT FK_1A7D46CEBF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_for_sale ADD CONSTRAINT FK_FD4ECB07BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_for_trade ADD CONSTRAINT FK_900C274134E1CA94 FOREIGN KEY (chosen_offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE product_for_trade ADD CONSTRAINT FK_900C2741BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBC6D2DC64 FOREIGN KEY (support_ticket_id) REFERENCES support_ticket (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE support_ticket ADD CONSTRAINT FK_1F5A4D53C78D6829 FOREIGN KEY (support_ticket_category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE support_ticket ADD CONSTRAINT FK_1F5A4D53A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE auction DROP FOREIGN KEY FK_DEE4F5934584665A');
        $this->addSql('ALTER TABLE bid DROP FOREIGN KEY FK_4AF2B3F3BE40AFAE');
        $this->addSql('ALTER TABLE bid DROP FOREIGN KEY FK_4AF2B3F357B8F0DE');
        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC10ECFE8C54');
        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC10FD128646');
        $this->addSql('ALTER TABLE delivery_man DROP FOREIGN KEY FK_4E0DFC7CBF396750');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7876C4DDA');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D229445871F7E88B');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944587597D3FE');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFD128646');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F4584665A');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FA76ED395');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78BF396750');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E4064D2CB');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E8A0E7271');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987597D3FE');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986C8A81A9');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBE6903FD');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7E3C61F9');
        $this->addSql('ALTER TABLE product_for_rent DROP FOREIGN KEY FK_1A7D46CEBF396750');
        $this->addSql('ALTER TABLE product_for_sale DROP FOREIGN KEY FK_FD4ECB07BF396750');
        $this->addSql('ALTER TABLE product_for_trade DROP FOREIGN KEY FK_900C274134E1CA94');
        $this->addSql('ALTER TABLE product_for_trade DROP FOREIGN KEY FK_900C2741BF396750');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBC6D2DC64');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBA76ED395');
        $this->addSql('ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D53C78D6829');
        $this->addSql('ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D53A76ED395');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE auction');
        $this->addSql('DROP TABLE bid');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE delivery_man');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_for_rent');
        $this->addSql('DROP TABLE product_for_sale');
        $this->addSql('DROP TABLE product_for_trade');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE support_ticket');
        $this->addSql('DROP TABLE user');
    }
}
