<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005234611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant CHANGE name name VARCHAR(80) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL, CHANGE address address VARCHAR(200) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE title title VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant CHANGE name name VARCHAR(80) NOT NULL, CHANGE phone_number phone_number VARCHAR(20) NOT NULL, CHANGE address address VARCHAR(200) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE website website VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) NOT NULL, CHANGE title title VARCHAR(100) NOT NULL');
    }
}
