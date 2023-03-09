<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307005325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE load_deductions (id INT AUTO_INCREMENT NOT NULL, loads_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, amount NUMERIC(20, 2) NOT NULL, INDEX IDX_88CAF19DD00848B3 (loads_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE load_deductions ADD CONSTRAINT FK_88CAF19DD00848B3 FOREIGN KEY (loads_id) REFERENCES loads (id)');
        $this->addSql('ALTER TABLE fuel_transactions DROP FOREIGN KEY fuel_transactions_ibfk_1');
        $this->addSql('ALTER TABLE fuel_transactions CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('DROP INDEX company_id ON fuel_transactions');
        $this->addSql('CREATE INDEX IDX_D017A4BF979B1AD6 ON fuel_transactions (company_id)');
        $this->addSql('ALTER TABLE fuel_transactions ADD CONSTRAINT fuel_transactions_ibfk_1 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE loads DROP FOREIGN KEY loads_ibfk_1');
        $this->addSql('ALTER TABLE loads DROP FOREIGN KEY loads_ibfk_2');
        $this->addSql('ALTER TABLE loads CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE fuel_surcharge fuel_surcharge NUMERIC(20, 2) NOT NULL, CHANGE well_name well_name VARCHAR(6444) NOT NULL');
        $this->addSql('DROP INDEX trier_id ON loads');
        $this->addSql('CREATE INDEX IDX_782DA53610D0539B ON loads (trier_id)');
        $this->addSql('DROP INDEX company ON loads');
        $this->addSql('CREATE INDEX IDX_782DA536979B1AD6 ON loads (company_id)');
        $this->addSql('ALTER TABLE loads ADD CONSTRAINT loads_ibfk_1 FOREIGN KEY (trier_id) REFERENCES trier (id)');
        $this->addSql('ALTER TABLE loads ADD CONSTRAINT loads_ibfk_2 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE reset_password_request CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user_company ADD truck VARCHAR(255) DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE load_deductions DROP FOREIGN KEY FK_88CAF19DD00848B3');
        $this->addSql('DROP TABLE load_deductions');
        $this->addSql('ALTER TABLE fuel_transactions DROP FOREIGN KEY FK_D017A4BF979B1AD6');
        $this->addSql('ALTER TABLE fuel_transactions CHANGE id id BIGINT AUTO_INCREMENT NOT NULL');
        $this->addSql('DROP INDEX idx_d017a4bf979b1ad6 ON fuel_transactions');
        $this->addSql('CREATE INDEX company_id ON fuel_transactions (company_id)');
        $this->addSql('ALTER TABLE fuel_transactions ADD CONSTRAINT FK_D017A4BF979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE loads DROP FOREIGN KEY FK_782DA53610D0539B');
        $this->addSql('ALTER TABLE loads DROP FOREIGN KEY FK_782DA536979B1AD6');
        $this->addSql('ALTER TABLE loads CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE fuel_surcharge fuel_surcharge NUMERIC(20, 2) DEFAULT \'0.00\' NOT NULL, CHANGE well_name well_name TEXT NOT NULL');
        $this->addSql('DROP INDEX idx_782da53610d0539b ON loads');
        $this->addSql('CREATE INDEX trier_id ON loads (trier_id)');
        $this->addSql('DROP INDEX idx_782da536979b1ad6 ON loads');
        $this->addSql('CREATE INDEX company ON loads (company_id)');
        $this->addSql('ALTER TABLE loads ADD CONSTRAINT FK_782DA53610D0539B FOREIGN KEY (trier_id) REFERENCES trier (id)');
        $this->addSql('ALTER TABLE loads ADD CONSTRAINT FK_782DA536979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE reset_password_request CHANGE user_id user_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE active active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE user_company DROP truck, CHANGE user_id user_id BIGINT DEFAULT NULL');
    }
}
