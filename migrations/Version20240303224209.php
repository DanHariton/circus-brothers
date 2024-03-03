<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240303224209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE concert (
                id INT AUTO_INCREMENT NOT NULL, 
                date DATETIME NOT NULL,
                name VARCHAR(255) NOT NULL, 
                location VARCHAR(255) NOT NULL,
                tickets VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE merch (id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL, 
                price INT NOT NULL, 
                active TINYINT(1) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE merchs_sizes (
                merch_id INT NOT NULL, 
                size_id INT NOT NULL, 
                INDEX IDX_MERCH (merch_id), 
                INDEX IDX_SIZE (size_id), 
                PRIMARY KEY(merch_id, size_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE size (
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE merchs_sizes ADD CONSTRAINT FK_MERCHS_SIZES_MERCH FOREIGN KEY (merch_id) REFERENCES merch (id)');
        $this->addSql('ALTER TABLE merchs_sizes ADD CONSTRAINT FK_MERCHS_SIZES_SIZE FOREIGN KEY (size_id) REFERENCES size (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE merchs_sizes DROP FOREIGN KEY FK_MERCHS_SIZES_MERCH');
        $this->addSql('ALTER TABLE merchs_sizes DROP FOREIGN KEY FK_MERCHS_SIZES_SIZE');
        $this->addSql('DROP TABLE concert');
        $this->addSql('DROP TABLE merch');
        $this->addSql('DROP TABLE merchs_sizes');
        $this->addSql('DROP TABLE size');
    }
}
