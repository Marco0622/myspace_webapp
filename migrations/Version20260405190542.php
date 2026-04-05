<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260405190542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD storage_id INT NOT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D45CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_D044D5D45CC5DB90 ON session (storage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP CONSTRAINT FK_D044D5D45CC5DB90');
        $this->addSql('DROP INDEX IDX_D044D5D45CC5DB90');
        $this->addSql('ALTER TABLE session DROP storage_id');
    }
}
