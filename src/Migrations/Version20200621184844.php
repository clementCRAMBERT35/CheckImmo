<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200621184844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE annonces (id INT AUTO_INCREMENT NOT NULL, type_bien VARCHAR(255) NOT NULL, departement VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, secteur VARCHAR(255) NOT NULL, anne_construction INT DEFAULT NULL, surface_habitable INT NOT NULL, nombre_pieces INT NOT NULL, nombre_chambres INT NOT NULL, surface_sejour INT DEFAULT NULL, etage VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, date_aspiration DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annonces_users (annonces_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_F60119834C2885D7 (annonces_id), INDEX IDX_F601198367B3B43D (users_id), PRIMARY KEY(annonces_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonces_users ADD CONSTRAINT FK_F60119834C2885D7 FOREIGN KEY (annonces_id) REFERENCES annonces (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE annonces_users ADD CONSTRAINT FK_F601198367B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL, CHANGE activation_token activation_token VARCHAR(50) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE annonces_users DROP FOREIGN KEY FK_F60119834C2885D7');
        $this->addSql('DROP TABLE annonces');
        $this->addSql('DROP TABLE annonces_users');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE activation_token activation_token VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_token reset_token VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
