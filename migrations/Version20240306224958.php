<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306224958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (
                        id INT AUTO_INCREMENT NOT NULL, 
                        merch_id INT DEFAULT NULL, 
                        file_name VARCHAR(255) NOT NULL, 
                        position INT NOT NULL, 
                        INDEX IDX_MERCH_ID (merch_id), 
                        PRIMARY KEY(id)
                  ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_content (
                        id INT AUTO_INCREMENT NOT NULL, 
                        file_id INT DEFAULT NULL, 
                        name VARCHAR(255) NOT NULL, 
                        alt_name VARCHAR(255) DEFAULT NULL, 
                        position INT NOT NULL, 
                        video_link LONGTEXT DEFAULT NULL, 
                        active TINYINT(1) NOT NULL, 
                        UNIQUE INDEX UNIQ_FILE_ID (file_id), 
                        PRIMARY KEY(id)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_FILE_MERCH FOREIGN KEY (merch_id) REFERENCES merch (id)');
        $this->addSql('ALTER TABLE media_content ADD CONSTRAINT FK_MEDIA_CONTENT_FILE FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_FILE_MERCH');
        $this->addSql('ALTER TABLE media_content DROP FOREIGN KEY FK_MEDIA_CONTENT_FILE');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE media_content');
    }
}
