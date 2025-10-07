<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005043931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accordion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accordion_item (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, name_hu VARCHAR(255) DEFAULT NULL, name_en VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, text_hu LONGTEXT DEFAULT NULL, text_en LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', order_num INT NOT NULL, INDEX IDX_183BB3AD727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accordion_item ADD CONSTRAINT FK_183BB3AD727ACA70 FOREIGN KEY (parent_id) REFERENCES accordion (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accordion_item DROP FOREIGN KEY FK_183BB3AD727ACA70');
        $this->addSql('DROP TABLE accordion');
        $this->addSql('DROP TABLE accordion_item');
    }
}
