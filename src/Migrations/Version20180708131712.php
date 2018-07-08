<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180708131712 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE keyword ADD site_id INT NOT NULL');
        $this->addSql('ALTER TABLE keyword ADD CONSTRAINT FK_5A93713BF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_5A93713BF6BD1646 ON keyword (site_id)');
        $this->addSql('ALTER TABLE query ADD keyword_id INT NOT NULL');
        $this->addSql('ALTER TABLE query ADD CONSTRAINT FK_24BDB5EB115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id)');
        $this->addSql('CREATE INDEX IDX_24BDB5EB115D4552 ON query (keyword_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE keyword DROP FOREIGN KEY FK_5A93713BF6BD1646');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP INDEX IDX_5A93713BF6BD1646 ON keyword');
        $this->addSql('ALTER TABLE keyword DROP site_id');
        $this->addSql('ALTER TABLE query DROP FOREIGN KEY FK_24BDB5EB115D4552');
        $this->addSql('DROP INDEX IDX_24BDB5EB115D4552 ON query');
        $this->addSql('ALTER TABLE query DROP keyword_id');
    }
}
