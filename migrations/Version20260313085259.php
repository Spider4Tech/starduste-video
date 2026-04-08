<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260313085259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Utilisateurs (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(36) NOT NULL, pseudo VARCHAR(24) NOT NULL, subscribers INT NOT NULL, join_date DATE DEFAULT NULL, uploaded_video INT NOT NULL, is_admin TINYINT DEFAULT NULL, age INT NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(50) NOT NULL, ip_adresse VARCHAR(255) DEFAULT NULL, last_login DATETIME DEFAULT NULL, pfppath VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_514AEAA6D17F50A6 (uuid), UNIQUE INDEX UNIQ_514AEAA6E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Utilisateurs');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
