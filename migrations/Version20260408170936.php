<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408170936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT fk_7ce748aa76ed395');
        $this->addSql('DROP INDEX idx_7ce748aa76ed395');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT reset_password_request_pkey');
        $this->addSql('ALTER TABLE reset_password_request RENAME COLUMN id TO rpr_id');
        $this->addSql('ALTER TABLE reset_password_request RENAME COLUMN user_id TO rpr_usr_id');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748A67D683ED FOREIGN KEY (rpr_usr_id) REFERENCES Users (usr_id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_7CE748A67D683ED ON reset_password_request (rpr_usr_id)');
        $this->addSql('ALTER TABLE reset_password_request ADD PRIMARY KEY (rpr_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748A67D683ED');
        $this->addSql('DROP INDEX IDX_7CE748A67D683ED');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT reset_password_request_pkey');
        $this->addSql('ALTER TABLE reset_password_request RENAME COLUMN rpr_id TO id');
        $this->addSql('ALTER TABLE reset_password_request RENAME COLUMN rpr_usr_id TO user_id');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT fk_7ce748aa76ed395 FOREIGN KEY (user_id) REFERENCES users (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7ce748aa76ed395 ON reset_password_request (user_id)');
        $this->addSql('ALTER TABLE reset_password_request ADD PRIMARY KEY (id)');
    }
}
