<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208211734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, role VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery_man (id INT NOT NULL, disponibility VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, area VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, delivery_man_id INT DEFAULT NULL, product_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, modify_date DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_C53D045FFD128646 (delivery_man_id), INDEX IDX_C53D045F4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, product_category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, approved TINYINT(1) DEFAULT NULL, add_date DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_D34A04ADBE6903FD (product_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_for_rent (id INT AUTO_INCREMENT NOT NULL, price_per_day DOUBLE PRECISION NOT NULL, negotiable TINYINT(1) NOT NULL, disponibility VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_for_sale (id INT AUTO_INCREMENT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_for_trade (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, support_ticket_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, add_date DATETIME NOT NULL, INDEX IDX_3E7B0BFBC6D2DC64 (support_ticket_id), INDEX IDX_3E7B0BFBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE support_ticket (id INT AUTO_INCREMENT NOT NULL, support_ticket_category_id INT NOT NULL, user_id INT NOT NULL, subject VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_1F5A4D53C78D6829 (support_ticket_category_id), INDEX IDX_1F5A4D53A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, cin INT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(50) NOT NULL, adress VARCHAR(255) NOT NULL, phone INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE delivery_man ADD CONSTRAINT FK_4E0DFC7CBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FFD128646 FOREIGN KEY (delivery_man_id) REFERENCES delivery_man (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBE6903FD FOREIGN KEY (product_category_id) REFERENCES category (id)');
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
        $this->addSql('ALTER TABLE delivery_man DROP FOREIGN KEY FK_4E0DFC7CBF396750');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFD128646');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F4584665A');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78BF396750');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBE6903FD');
        $this->addSql('ALTER TABLE product_for_trade DROP FOREIGN KEY FK_900C2741BF396750');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBC6D2DC64');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBA76ED395');
        $this->addSql('ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D53C78D6829');
        $this->addSql('ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D53A76ED395');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE delivery_man');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_for_rent');
        $this->addSql('DROP TABLE product_for_sale');
        $this->addSql('DROP TABLE product_for_trade');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE support_ticket');
        $this->addSql('DROP TABLE user');
    }
}
