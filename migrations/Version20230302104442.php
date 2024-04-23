<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302104442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495521BDB235');
        $this->addSql('ALTER TABLE reservation DROP heure_fin, CHANGE date_debut date_debut DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE date_fin date_fin DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495521BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495521BDB235');
        $this->addSql('ALTER TABLE reservation ADD heure_fin INT NOT NULL, CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495521BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON DELETE CASCADE');
    }
}
