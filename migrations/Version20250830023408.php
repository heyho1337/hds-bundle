<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250830023408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL, CHANGE text_hu text_hu LONGTEXT DEFAULT NULL, CHANGE title_hu title_hu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE blog CHANGE slug_hu slug_hu VARCHAR(255) DEFAULT NULL, CHANGE slug_en slug_en VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL, CHANGE title_hu title_hu VARCHAR(255) DEFAULT NULL, CHANGE slug_hu slug_hu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD tag_id INT DEFAULT NULL, DROP title_hu, DROP title_en, DROP meta_desc_hu, DROP meta_desc_en, CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL, CHANGE slug_hu slug_hu VARCHAR(255) DEFAULT NULL, CHANGE slug_en slug_en VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('CREATE INDEX IDX_7D053A93BAD26311 ON menu (tag_id)');
        $this->addSql('ALTER TABLE menu_position CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_target CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_type ADD name_hu VARCHAR(255) DEFAULT NULL, ADD name_en VARCHAR(255) DEFAULT NULL, DROP name');
        $this->addSql('ALTER TABLE tag CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL, CHANGE slug_hu slug_hu VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE name_hu name_hu VARCHAR(255) NOT NULL, CHANGE text_hu text_hu LONGTEXT NOT NULL, CHANGE title_hu title_hu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE blog CHANGE slug_hu slug_hu VARCHAR(255) NOT NULL, CHANGE slug_en slug_en VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category CHANGE name_hu name_hu VARCHAR(255) NOT NULL, CHANGE title_hu title_hu VARCHAR(255) NOT NULL, CHANGE slug_hu slug_hu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93BAD26311');
        $this->addSql('DROP INDEX IDX_7D053A93BAD26311 ON menu');
        $this->addSql('ALTER TABLE menu ADD title_hu VARCHAR(255) NOT NULL, ADD title_en VARCHAR(255) DEFAULT NULL, ADD meta_desc_hu VARCHAR(255) NOT NULL, ADD meta_desc_en VARCHAR(255) DEFAULT NULL, DROP tag_id, CHANGE name_hu name_hu VARCHAR(255) NOT NULL, CHANGE slug_hu slug_hu VARCHAR(255) NOT NULL, CHANGE slug_en slug_en VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE menu_position CHANGE name_hu name_hu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE menu_target CHANGE name_hu name_hu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE menu_type ADD name VARCHAR(55) NOT NULL, DROP name_hu, DROP name_en');
        $this->addSql('ALTER TABLE tag CHANGE name_hu name_hu VARCHAR(255) NOT NULL, CHANGE slug_hu slug_hu VARCHAR(255) NOT NULL');
    }
}
