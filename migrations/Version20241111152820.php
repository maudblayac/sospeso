<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241111152820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant_featured_products DROP FOREIGN KEY FK_23EA44A74584665A');
        $this->addSql('ALTER TABLE restaurant_featured_products DROP FOREIGN KEY FK_23EA44A7B1E7706E');
        $this->addSql('DROP TABLE restaurant_featured_products');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE restaurant_featured_products (restaurant_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_23EA44A74584665A (product_id), INDEX IDX_23EA44A7B1E7706E (restaurant_id), PRIMARY KEY(restaurant_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE restaurant_featured_products ADD CONSTRAINT FK_23EA44A74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_featured_products ADD CONSTRAINT FK_23EA44A7B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
