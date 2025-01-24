<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105152905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE foundry (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, owner_id INT DEFAULT NULL, INDEX IDX_1D6D45B17E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE foundry_owner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE orm_redirects (host VARCHAR(255) NOT NULL, schemes JSON NOT NULL, methods JSON NOT NULL, defaults JSON NOT NULL, requirements JSON NOT NULL, options JSON NOT NULL, `condition` VARCHAR(255) NOT NULL, variable_pattern VARCHAR(255) DEFAULT NULL, staticPrefix VARCHAR(255) DEFAULT NULL, routeName VARCHAR(255) NOT NULL, uri VARCHAR(255) DEFAULT NULL, permanent TINYINT(1) NOT NULL, id INT AUTO_INCREMENT NOT NULL, routeTargetId INT DEFAULT NULL, UNIQUE INDEX UNIQ_6CA17E0391F30BA8 (routeName), INDEX IDX_6CA17E034C0848C6 (routeTargetId), INDEX IDX_6CA17E03A5B5867E (staticPrefix), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE orm_routes (host VARCHAR(255) NOT NULL, schemes JSON NOT NULL, methods JSON NOT NULL, defaults JSON NOT NULL, requirements JSON NOT NULL, options JSON NOT NULL, `condition` VARCHAR(255) NOT NULL, variable_pattern VARCHAR(255) DEFAULT NULL, staticPrefix VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT NOT NULL, id INT AUTO_INCREMENT NOT NULL, INDEX IDX_5793FCA5B5867E (staticPrefix), UNIQUE INDEX name_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pizza (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE todo (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE foundry ADD CONSTRAINT FK_1D6D45B17E3C61F9 FOREIGN KEY (owner_id) REFERENCES foundry_owner (id)');
        $this->addSql('ALTER TABLE orm_redirects ADD CONSTRAINT FK_6CA17E034C0848C6 FOREIGN KEY (routeTargetId) REFERENCES orm_routes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foundry DROP FOREIGN KEY FK_1D6D45B17E3C61F9');
        $this->addSql('ALTER TABLE orm_redirects DROP FOREIGN KEY FK_6CA17E034C0848C6');
        $this->addSql('DROP TABLE foundry');
        $this->addSql('DROP TABLE foundry_owner');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE orm_redirects');
        $this->addSql('DROP TABLE orm_routes');
        $this->addSql('DROP TABLE pizza');
        $this->addSql('DROP TABLE todo');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
