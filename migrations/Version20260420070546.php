<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260420070546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accesses DROP CONSTRAINT fk_1b42a8b4456fb2bb');
        $this->addSql('ALTER TABLE accesses ADD CONSTRAINT FK_1B42A8B4456FB2BB FOREIGN KEY (acc_session_id) REFERENCES Sessions (ses_id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE invitations DROP CONSTRAINT fk_a5bb9f79a5a1561f');
        $this->addSql('ALTER TABLE invitations ADD CONSTRAINT FK_A5BB9F79A5A1561F FOREIGN KEY (inv_session_id) REFERENCES Sessions (ses_id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE nodes DROP CONSTRAINT fk_dcfc2af8fdd95639');
        $this->addSql('ALTER TABLE nodes ADD nod_add_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE nodes ADD CONSTRAINT FK_DCFC2AF8FDD95639 FOREIGN KEY (nod_session_id) REFERENCES Sessions (ses_id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE pages DROP CONSTRAINT fk_e1b5ca71dd88e195');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_E1B5CA71DD88E195 FOREIGN KEY (pag_session_id) REFERENCES Sessions (ses_id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE pictures DROP CONSTRAINT fk_8f7c2fc0848d0987');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_760A4D96848D0987 FOREIGN KEY (pic_session_id) REFERENCES Sessions (ses_id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER INDEX idx_8f7c2fc0848d0987 RENAME TO IDX_760A4D96848D0987');
        $this->addSql('ALTER INDEX idx_8f7c2fc06c562001 RENAME TO IDX_760A4D966C562001');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Accesses DROP CONSTRAINT FK_1B42A8B4456FB2BB');
        $this->addSql('ALTER TABLE Accesses ADD CONSTRAINT fk_1b42a8b4456fb2bb FOREIGN KEY (acc_session_id) REFERENCES sessions (ses_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE Invitations DROP CONSTRAINT FK_A5BB9F79A5A1561F');
        $this->addSql('ALTER TABLE Invitations ADD CONSTRAINT fk_a5bb9f79a5a1561f FOREIGN KEY (inv_session_id) REFERENCES sessions (ses_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE Nodes DROP CONSTRAINT FK_DCFC2AF8FDD95639');
        $this->addSql('ALTER TABLE Nodes DROP nod_add_at');
        $this->addSql('ALTER TABLE Nodes ADD CONSTRAINT fk_dcfc2af8fdd95639 FOREIGN KEY (nod_session_id) REFERENCES sessions (ses_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE Pages DROP CONSTRAINT FK_E1B5CA71DD88E195');
        $this->addSql('ALTER TABLE Pages ADD CONSTRAINT fk_e1b5ca71dd88e195 FOREIGN KEY (pag_session_id) REFERENCES sessions (ses_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE Pictures DROP CONSTRAINT FK_760A4D96848D0987');
        $this->addSql('ALTER TABLE Pictures ADD CONSTRAINT fk_8f7c2fc0848d0987 FOREIGN KEY (pic_session_id) REFERENCES sessions (ses_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_760a4d96848d0987 RENAME TO idx_8f7c2fc0848d0987');
        $this->addSql('ALTER INDEX idx_760a4d966c562001 RENAME TO idx_8f7c2fc06c562001');
    }
}
