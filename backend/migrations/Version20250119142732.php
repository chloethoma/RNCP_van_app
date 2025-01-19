<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250119142732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE spot (id SERIAL NOT NULL, owner_id INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, description TEXT DEFAULT NULL, is_favorite BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B9327A737E3C61F9 ON spot (owner_id)');
        $this->addSql('CREATE TABLE spot_picture (id SERIAL NOT NULL, spot_id INT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C317B212DF1D37C ON spot_picture (spot_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, email_verified BOOLEAN NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(30) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, picture VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A737E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot_picture ADD CONSTRAINT FK_5C317B212DF1D37C FOREIGN KEY (spot_id) REFERENCES spot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A737E3C61F9');
        $this->addSql('ALTER TABLE spot_picture DROP CONSTRAINT FK_5C317B212DF1D37C');
        $this->addSql('DROP TABLE spot');
        $this->addSql('DROP TABLE spot_picture');
        $this->addSql('DROP TABLE "user"');
    }
}
