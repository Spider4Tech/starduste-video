<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260601010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add live stream support.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE Utilisateurs ADD live_stream_key VARCHAR(64) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_514AEAA6F3BE9D10 ON Utilisateurs (live_stream_key)');
        $this->addSql('CREATE TABLE live_stream (id INT AUTO_INCREMENT NOT NULL, streamer_id INT NOT NULL, slug VARCHAR(32) NOT NULL, title VARCHAR(128) NOT NULL, category VARCHAR(80) DEFAULT NULL, thumbnail_path VARCHAR(255) DEFAULT NULL, playback_url VARCHAR(255) DEFAULT NULL, live TINYINT(1) NOT NULL, viewers INT NOT NULL, started_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_53B48F53B4216C33 (streamer_id), UNIQUE INDEX UNIQ_53B48F53989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE live_stream ADD CONSTRAINT FK_53B48F53B4216C33 FOREIGN KEY (streamer_id) REFERENCES Utilisateurs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE live_stream DROP FOREIGN KEY FK_53B48F53B4216C33');
        $this->addSql('DROP TABLE live_stream');
        $this->addSql('DROP INDEX UNIQ_514AEAA6F3BE9D10 ON Utilisateurs');
        $this->addSql('ALTER TABLE Utilisateurs DROP live_stream_key');
    }
}
