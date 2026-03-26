<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320100947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(16) NOT NULL, like_vid INT NOT NULL, title VARCHAR(28) NOT NULL, video_duration INT NOT NULL, video_url VARCHAR(255) DEFAULT NULL, thumbnail VARCHAR(255) DEFAULT NULL, upload_date DATETIME DEFAULT NULL, dislike_vid INT NOT NULL, description VARCHAR(1024) DEFAULT NULL, status TINYINT NOT NULL, categorie VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7CC7DA2CD17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE Utilisateurs CHANGE join_date JOIN_DATE DATE DEFAULT NULL, CHANGE ip_adresse IP_ADRESSE VARCHAR(255) DEFAULT NULL, CHANGE last_login LAST_LOGIN DATETIME DEFAULT NULL, CHANGE pfppath pfppath VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE Utilisateurs RENAME INDEX uniq_514aeaa6e7927c74 TO UNIQ_514AEAA610C6BEC4');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE video');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE Utilisateurs CHANGE JOIN_DATE join_date DATE DEFAULT \'NULL\', CHANGE IP_ADRESSE ip_adresse VARCHAR(255) DEFAULT \'NULL\', CHANGE LAST_LOGIN last_login DATETIME DEFAULT \'NULL\', CHANGE pfppath pfppath VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE Utilisateurs RENAME INDEX uniq_514aeaa610c6bec4 TO UNIQ_514AEAA6E7927C74');
    }
}
