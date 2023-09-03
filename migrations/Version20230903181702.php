<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903181702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE game_action_field_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE game_action_field (id INT NOT NULL, action_field_id INT NOT NULL, game_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_52EBBD8A93E077D6 ON game_action_field (action_field_id)');
        $this->addSql('CREATE INDEX IDX_52EBBD8AE48FD905 ON game_action_field (game_id)');
        $this->addSql('ALTER TABLE game_action_field ADD CONSTRAINT FK_52EBBD8A93E077D6 FOREIGN KEY (action_field_id) REFERENCES action_field (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_action_field ADD CONSTRAINT FK_52EBBD8AE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE building ALTER "position" TYPE VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE game_action_field_id_seq CASCADE');
        $this->addSql('ALTER TABLE game_action_field DROP CONSTRAINT FK_52EBBD8A93E077D6');
        $this->addSql('ALTER TABLE game_action_field DROP CONSTRAINT FK_52EBBD8AE48FD905');
        $this->addSql('DROP TABLE game_action_field');
        $this->addSql('ALTER TABLE building ALTER position TYPE INT');
    }
}
