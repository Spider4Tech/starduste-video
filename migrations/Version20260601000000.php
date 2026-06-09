<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260601000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add banner path column to users.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE Utilisateurs ADD bannerpath VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE Utilisateurs DROP bannerpath');
    }
}
