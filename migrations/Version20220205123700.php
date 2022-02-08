<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220205123700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ngd_product_line (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_published TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ngd_warehouse (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', owner_id INT NOT NULL, count_stock INT DEFAULT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_503B2C64584665A (product_id), INDEX IDX_503B2C67E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ngd_warehouse ADD CONSTRAINT FK_503B2C64584665A FOREIGN KEY (product_id) REFERENCES ngd_product_line (id)');
        $this->addSql('ALTER TABLE ngd_warehouse ADD CONSTRAINT FK_503B2C67E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ngd_warehouse DROP FOREIGN KEY FK_503B2C64584665A');
        $this->addSql('ALTER TABLE ngd_warehouse DROP FOREIGN KEY FK_503B2C67E3C61F9');
        $this->addSql('DROP TABLE ngd_product_line');
        $this->addSql('DROP TABLE ngd_warehouse');
        $this->addSql('DROP TABLE user');
    }
}
