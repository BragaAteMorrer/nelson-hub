<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260928123000 extends AbstractMigration
{
    public function getDescription(): string { return 'Create independent Nelson Hub user accounts.'; }
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE hub_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, display_name VARCHAR(80) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, settings CLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)");
        $this->addSql("CREATE UNIQUE INDEX uniq_hub_user_email ON hub_user (email)");
    }
    public function down(Schema $schema): void { $this->addSql('DROP TABLE hub_user'); }
}
