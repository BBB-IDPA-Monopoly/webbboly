<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230909024939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD funds INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_action_field ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_action_field ADD CONSTRAINT FK_52EBBD8A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_52EBBD8A7E3C61F9 ON game_action_field (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE game DROP funds');
        $this->addSql('ALTER TABLE game_action_field DROP CONSTRAINT FK_52EBBD8A7E3C61F9');
        $this->addSql('DROP INDEX IDX_52EBBD8A7E3C61F9');
        $this->addSql('ALTER TABLE game_action_field DROP owner_id');
    }
}
