<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210311141406 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_row ADD order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_row ADD CONSTRAINT FK_C76BB9BB8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_C76BB9BB8D9F6D38 ON order_row (order_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_row DROP FOREIGN KEY FK_C76BB9BB8D9F6D38');
        $this->addSql('DROP INDEX IDX_C76BB9BB8D9F6D38 ON order_row');
        $this->addSql('ALTER TABLE order_row DROP order_id');
    }
}
