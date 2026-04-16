<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416114214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nodes ADD nod_add_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER INDEX idx_8f7c2fc0848d0987 RENAME TO IDX_760A4D96848D0987');
        $this->addSql('ALTER INDEX idx_8f7c2fc06c562001 RENAME TO IDX_760A4D966C562001');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Nodes DROP nod_add_at');
        $this->addSql('ALTER INDEX idx_760a4d96848d0987 RENAME TO idx_8f7c2fc0848d0987');
        $this->addSql('ALTER INDEX idx_760a4d966c562001 RENAME TO idx_8f7c2fc06c562001');
    }
}
