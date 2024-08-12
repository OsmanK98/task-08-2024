<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240812172133 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bank_account (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', owner_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', account_number VARCHAR(255) NOT NULL, balance INT NOT NULL, currency VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', sender_bank_account_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', receiver_bank_account_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', sender_account_number VARCHAR(255) NOT NULL, receiver_account_number VARCHAR(255) NOT NULL, amount INT NOT NULL, fee INT NOT NULL, currency VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, transaction_date DATE NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_723705D11077E6FB (sender_bank_account_id), INDEX IDX_723705D1D680A500 (receiver_bank_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D11077E6FB FOREIGN KEY (sender_bank_account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D680A500 FOREIGN KEY (receiver_bank_account_id) REFERENCES bank_account (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D11077E6FB');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1D680A500');
        $this->addSql('DROP TABLE bank_account');
        $this->addSql('DROP TABLE transaction');
    }
}
