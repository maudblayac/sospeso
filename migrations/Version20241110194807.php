<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241110194807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE featured_product DROP FOREIGN KEY FK_728B14E04584665A');
        $this->addSql('ALTER TABLE featured_product DROP FOREIGN KEY FK_728B14E0B1E7706E');
        $this->addSql('ALTER TABLE featured_product ADD CONSTRAINT FK_728B14E04584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE featured_product ADD CONSTRAINT FK_728B14E0B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE featured_product DROP FOREIGN KEY FK_728B14E04584665A');
        $this->addSql('ALTER TABLE featured_product DROP FOREIGN KEY FK_728B14E0B1E7706E');
        $this->addSql('ALTER TABLE featured_product ADD CONSTRAINT FK_728B14E04584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE featured_product ADD CONSTRAINT FK_728B14E0B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
