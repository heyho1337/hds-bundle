<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251004030415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name_hu VARCHAR(255) DEFAULT NULL, name_en VARCHAR(255) DEFAULT NULL, title_hu VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, meta_desc_hu VARCHAR(255) DEFAULT NULL, meta_desc_en VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', active TINYINT(1) NOT NULL, slug_hu VARCHAR(255) DEFAULT NULL, slug_en VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, text_hu LONGTEXT DEFAULT NULL, text_en LONGTEXT DEFAULT NULL, short_desc VARCHAR(255) DEFAULT NULL, short_desc_hu VARCHAR(255) DEFAULT NULL, short_desc_en VARCHAR(255) DEFAULT NULL, INDEX IDX_472B783A727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery_image (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, image VARCHAR(255) NOT NULL, alt_hu VARCHAR(255) DEFAULT NULL, alt_en VARCHAR(255) DEFAULT NULL, title_hu VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, short_desc_hu VARCHAR(255) DEFAULT NULL, short_desc_en VARCHAR(255) DEFAULT NULL, name_hu VARCHAR(255) DEFAULT NULL, name_en VARCHAR(255) DEFAULT NULL, modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', active TINYINT(1) NOT NULL, INDEX IDX_21A0D47C727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783A727ACA70 FOREIGN KEY (parent_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE gallery_image ADD CONSTRAINT FK_21A0D47C727ACA70 FOREIGN KEY (parent_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE slide ADD slug_hu VARCHAR(255) DEFAULT NULL, ADD slug_en VARCHAR(255) DEFAULT NULL, DROP alias_hu, DROP alias_en');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783A727ACA70');
        $this->addSql('ALTER TABLE gallery_image DROP FOREIGN KEY FK_21A0D47C727ACA70');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE gallery_image');
        $this->addSql('ALTER TABLE slide ADD alias_hu VARCHAR(255) DEFAULT NULL, ADD alias_en VARCHAR(255) DEFAULT NULL, DROP slug_hu, DROP slug_en');
    }
}
