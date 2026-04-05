<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260405185742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT fk_f11d61a26061f7cf');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT fk_f11d61a2be20cab0');
        $this->addSql('DROP INDEX idx_f11d61a26061f7cf');
        $this->addSql('DROP INDEX idx_f11d61a2be20cab0');
        $this->addSql('ALTER TABLE invitation ADD sender_id INT NOT NULL');
        $this->addSql('ALTER TABLE invitation ADD receiver_id INT NOT NULL');
        $this->addSql('ALTER TABLE invitation DROP sender_id_id');
        $this->addSql('ALTER TABLE invitation DROP receiver_id_id');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_F11D61A2F624B39D ON invitation (sender_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2CD53EDB6 ON invitation (receiver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2F624B39D');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2CD53EDB6');
        $this->addSql('DROP INDEX IDX_F11D61A2F624B39D');
        $this->addSql('DROP INDEX IDX_F11D61A2CD53EDB6');
        $this->addSql('ALTER TABLE invitation ADD sender_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE invitation ADD receiver_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE invitation DROP sender_id');
        $this->addSql('ALTER TABLE invitation DROP receiver_id');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT fk_f11d61a26061f7cf FOREIGN KEY (sender_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT fk_f11d61a2be20cab0 FOREIGN KEY (receiver_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f11d61a26061f7cf ON invitation (sender_id_id)');
        $this->addSql('CREATE INDEX idx_f11d61a2be20cab0 ON invitation (receiver_id_id)');
    }
}
