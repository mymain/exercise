<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230824122934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create transaction table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE transaction (
                id INT AUTO_INCREMENT NOT NULL, 
                payment_method VARCHAR(32) NOT NULL, 
                transaction_type VARCHAR(32) NOT NULL, 
                transaction_timestamp INT NOT NULL, 
                base_amount INT NOT NULL, 
                base_currency VARCHAR(3) NOT NULL, 
                target_amount INT NOT NULL, 
                target_currency VARCHAR(3) NOT NULL, 
                exchange_rate VARCHAR(32) NOT NULL, 
                ip VARCHAR(128) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB 
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            DROP TABLE transaction
        SQL);
    }
}
