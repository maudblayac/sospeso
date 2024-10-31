<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031082636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant ADD lastname VARCHAR(80) DEFAULT NULL, ADD city VARCHAR(50) DEFAULT NULL, ADD country VARCHAR(100) DEFAULT NULL, ADD postal_code VARCHAR(8) DEFAULT NULL, ADD date_of_birth DATE DEFAULT NULL, CHANGE address address VARCHAR(100) DEFAULT NULL, CHANGE name firstname VARCHAR(80) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant ADD name VARCHAR(80) DEFAULT NULL, DROP firstname, DROP lastname, DROP city, DROP country, DROP postal_code, DROP date_of_birth, CHANGE address address VARCHAR(200) DEFAULT NULL');
    }
}
