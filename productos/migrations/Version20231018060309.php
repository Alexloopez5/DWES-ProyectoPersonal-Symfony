<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018060309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE producto ADD proveedor_id INT NOT NULL');
        // $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB0615CB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id)');
        // $this->addSql('CREATE INDEX IDX_A7BB0615CB305D73 ON producto (proveedor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB0615CB305D73');
        $this->addSql('DROP INDEX IDX_A7BB0615CB305D73 ON producto');
        $this->addSql('ALTER TABLE producto DROP proveedor_id');
    }
}
