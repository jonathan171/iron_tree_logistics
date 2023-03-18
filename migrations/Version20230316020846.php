<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316020846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_document (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cdl VARCHAR(255) NOT NULL, medical_card VARCHAR(255) NOT NULL, h2s VARCHAR(255) NOT NULL, pec VARCHAR(255) NOT NULL, cuestionario VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_38E46E76A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_document ADD CONSTRAINT FK_38E46E76A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE fuel_transactions CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE settlement_id settlement_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fuel_transactions RENAME INDEX company_id TO IDX_D017A4BF979B1AD6');
        $this->addSql('ALTER TABLE load_deductions CHANGE loads_id loads_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE loads CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE fuel_surcharge fuel_surcharge NUMERIC(20, 2) NOT NULL, CHANGE well_name well_name VARCHAR(6444) NOT NULL, CHANGE bol bol VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE loads RENAME INDEX trier_id TO IDX_782DA53610D0539B');
        $this->addSql('ALTER TABLE loads RENAME INDEX company TO IDX_782DA536979B1AD6');
        $this->addSql('ALTER TABLE reset_password_request CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE active active TINYINT(1) NOT NULL, CHANGE driver_name driver_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_company CHANGE user_id user_id INT DEFAULT NULL, CHANGE truck truck VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_document DROP FOREIGN KEY FK_38E46E76A76ED395');
        $this->addSql('DROP TABLE user_document');
        $this->addSql('ALTER TABLE fuel_transactions CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE settlement_id settlement_id VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fuel_transactions RENAME INDEX idx_d017a4bf979b1ad6 TO company_id');
        $this->addSql('ALTER TABLE loads CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE fuel_surcharge fuel_surcharge NUMERIC(20, 2) DEFAULT \'0.00\' NOT NULL, CHANGE well_name well_name TEXT NOT NULL, CHANGE bol bol VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE loads RENAME INDEX idx_782da536979b1ad6 TO company');
        $this->addSql('ALTER TABLE loads RENAME INDEX idx_782da53610d0539b TO trier_id');
        $this->addSql('ALTER TABLE load_deductions CHANGE loads_id loads_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reset_password_request CHANGE user_id user_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE active active TINYINT(1) DEFAULT 1 NOT NULL, CHANGE driver_name driver_name VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user_company CHANGE user_id user_id BIGINT DEFAULT NULL, CHANGE truck truck VARCHAR(255) DEFAULT \'NULL\'');
    }
}
