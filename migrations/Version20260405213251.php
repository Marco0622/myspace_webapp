<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260405213251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nodes ADD nod_parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nodes ADD CONSTRAINT FK_DCFC2AF89DC685B4 FOREIGN KEY (nod_parent_id) REFERENCES Nodes (nod_id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_DCFC2AF89DC685B4 ON nodes (nod_parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Nodes DROP CONSTRAINT FK_DCFC2AF89DC685B4');
        $this->addSql('DROP INDEX IDX_DCFC2AF89DC685B4');
        $this->addSql('ALTER TABLE Nodes DROP nod_parent_id');
    }
}
