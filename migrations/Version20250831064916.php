<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250831064916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config (id INT AUTO_INCREMENT NOT NULL, schema_type_id INT DEFAULT NULL, title_hu VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, meta_desc_hu VARCHAR(255) DEFAULT NULL, meta_desc_en VARCHAR(255) DEFAULT NULL, favicon VARCHAR(255) DEFAULT NULL, apple_icon VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, schema_text LONGTEXT DEFAULT NULL, INDEX IDX_D48A2F7C4076D1E5 (schema_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `schema` (id INT AUTO_INCREMENT NOT NULL, name_hu VARCHAR(255) DEFAULT NULL, name_en VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', code VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7C4076D1E5 FOREIGN KEY (schema_type_id) REFERENCES `schema` (id)');
        $this->addSql('ALTER TABLE menu_position ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE menu_target ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE menu_type ADD active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7C4076D1E5');
        $this->addSql('DROP TABLE config');
        $this->addSql('DROP TABLE `schema`');
        $this->addSql('ALTER TABLE menu_position DROP active');
        $this->addSql('ALTER TABLE menu_target DROP active');
        $this->addSql('ALTER TABLE menu_type DROP active');
    }
}
