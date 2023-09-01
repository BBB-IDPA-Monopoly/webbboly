<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901135238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE action_field_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE building_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE card_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_building_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_card_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE street_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE action_field (id INT NOT NULL, name VARCHAR(255) NOT NULL, function VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE building (id INT NOT NULL, street_id INT NOT NULL, name VARCHAR(255) NOT NULL, unit_rent INT NOT NULL, street_rent INT NOT NULL, single_house_rent INT NOT NULL, double_house_rent INT NOT NULL, triple_house_rent INT NOT NULL, quadruple_house_rent INT NOT NULL, hotel_rent INT NOT NULL, mortgage INT NOT NULL, mortgage_fee INT NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E16F61D487CF8EB ON building (street_id)');
        $this->addSql('CREATE TABLE card (id INT NOT NULL, text TEXT NOT NULL, type VARCHAR(255) NOT NULL, function VARCHAR(255) NOT NULL, amount_per_game INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE game_building (id INT NOT NULL, building_id INT NOT NULL, owner_id INT DEFAULT NULL, game_id INT NOT NULL, houses INT NOT NULL, mortgaged BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4A2B9EF24D2A7E12 ON game_building (building_id)');
        $this->addSql('CREATE INDEX IDX_4A2B9EF27E3C61F9 ON game_building (owner_id)');
        $this->addSql('CREATE INDEX IDX_4A2B9EF2E48FD905 ON game_building (game_id)');
        $this->addSql('CREATE TABLE game_card (id INT NOT NULL, card_id INT NOT NULL, owner_id INT DEFAULT NULL, game_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FD01F4FF4ACC9A20 ON game_card (card_id)');
        $this->addSql('CREATE INDEX IDX_FD01F4FF7E3C61F9 ON game_card (owner_id)');
        $this->addSql('CREATE INDEX IDX_FD01F4FFE48FD905 ON game_card (game_id)');
        $this->addSql('CREATE TABLE street (id INT NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D487CF8EB FOREIGN KEY (street_id) REFERENCES street (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_building ADD CONSTRAINT FK_4A2B9EF24D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_building ADD CONSTRAINT FK_4A2B9EF27E3C61F9 FOREIGN KEY (owner_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_building ADD CONSTRAINT FK_4A2B9EF2E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_card ADD CONSTRAINT FK_FD01F4FF4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_card ADD CONSTRAINT FK_FD01F4FF7E3C61F9 FOREIGN KEY (owner_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_card ADD CONSTRAINT FK_FD01F4FFE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE action_field_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE building_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE card_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_building_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_card_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE street_id_seq CASCADE');
        $this->addSql('ALTER TABLE building DROP CONSTRAINT FK_E16F61D487CF8EB');
        $this->addSql('ALTER TABLE game_building DROP CONSTRAINT FK_4A2B9EF24D2A7E12');
        $this->addSql('ALTER TABLE game_building DROP CONSTRAINT FK_4A2B9EF27E3C61F9');
        $this->addSql('ALTER TABLE game_building DROP CONSTRAINT FK_4A2B9EF2E48FD905');
        $this->addSql('ALTER TABLE game_card DROP CONSTRAINT FK_FD01F4FF4ACC9A20');
        $this->addSql('ALTER TABLE game_card DROP CONSTRAINT FK_FD01F4FF7E3C61F9');
        $this->addSql('ALTER TABLE game_card DROP CONSTRAINT FK_FD01F4FFE48FD905');
        $this->addSql('DROP TABLE action_field');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE game_building');
        $this->addSql('DROP TABLE game_card');
        $this->addSql('DROP TABLE street');
    }
}
