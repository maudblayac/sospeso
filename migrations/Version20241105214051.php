<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105214051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD restaurant_id INT DEFAULT NULL, CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FB1E7706E ON image (restaurant_id)');
        $this->addSql('ALTER TABLE restaurant ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB95123F3DA5256D ON restaurant (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FB1E7706E');
        $this->addSql('DROP INDEX UNIQ_C53D045FB1E7706E ON image');
        $this->addSql('ALTER TABLE image DROP restaurant_id, CHANGE product_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123F3DA5256D');
        $this->addSql('DROP INDEX UNIQ_EB95123F3DA5256D ON restaurant');
        $this->addSql('ALTER TABLE restaurant DROP image_id');
    }
}
