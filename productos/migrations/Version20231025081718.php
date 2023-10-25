<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231025081718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE producto CHANGE proveedor_id proveedor_id INT NOT NULL');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB0615CB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id)');
        $this->addSql('CREATE INDEX IDX_A7BB0615CB305D73 ON producto (proveedor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB0615CB305D73');
        $this->addSql('DROP INDEX IDX_A7BB0615CB305D73 ON producto');
        $this->addSql('ALTER TABLE producto CHANGE proveedor_id proveedor_id INT DEFAULT NULL');
    }
}
