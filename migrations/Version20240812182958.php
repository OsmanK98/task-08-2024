<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240812182958 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction CHANGE sender_bank_account_id sender_bank_account_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction CHANGE sender_bank_account_id sender_bank_account_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }
}
