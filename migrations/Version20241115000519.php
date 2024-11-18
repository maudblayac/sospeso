<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241115000519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD status VARCHAR(255) NOT NULL, DROP is_archived');
        $this->addSql('ALTER TABLE restaurant ADD status VARCHAR(255) NOT NULL, DROP is_archived');
        $this->addSql('ALTER TABLE user DROP is_archived');
        $this->addSql('ALTER TABLE user_profile ADD status VARCHAR(255) NOT NULL, DROP is_archived');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD is_archived TINYINT(1) NOT NULL, DROP status');
        $this->addSql('ALTER TABLE restaurant ADD is_archived TINYINT(1) NOT NULL, DROP status');
        $this->addSql('ALTER TABLE user ADD is_archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user_profile ADD is_archived TINYINT(1) NOT NULL, DROP status');
    }
}
