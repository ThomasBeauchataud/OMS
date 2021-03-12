<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210311161657 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD external_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_row ADD external_id INT NOT NULL, ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE transmitter CHANGE folder folder VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP external_id');
        $this->addSql('ALTER TABLE order_row DROP ean, DROP quantity');
        $this->addSql('ALTER TABLE transmitter CHANGE folder folder VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
