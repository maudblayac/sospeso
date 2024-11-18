<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241110184939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE featured_product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, restaurant_id INT NOT NULL, INDEX IDX_728B14E04584665A (product_id), INDEX IDX_728B14E0B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE featured_product ADD CONSTRAINT FK_728B14E04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE featured_product ADD CONSTRAINT FK_728B14E0B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE restaurant_product DROP FOREIGN KEY FK_190158D84584665A');
        $this->addSql('ALTER TABLE restaurant_product DROP FOREIGN KEY FK_190158D8B1E7706E');
        $this->addSql('DROP TABLE restaurant_product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE restaurant_product (restaurant_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_190158D84584665A (product_id), INDEX IDX_190158D8B1E7706E (restaurant_id), PRIMARY KEY(restaurant_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE restaurant_product ADD CONSTRAINT FK_190158D84584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_product ADD CONSTRAINT FK_190158D8B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE featured_product DROP FOREIGN KEY FK_728B14E04584665A');
        $this->addSql('ALTER TABLE featured_product DROP FOREIGN KEY FK_728B14E0B1E7706E');
        $this->addSql('DROP TABLE featured_product');
    }
}
