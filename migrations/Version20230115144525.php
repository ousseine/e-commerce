<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230115144525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, from_order_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , paid_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , validated BOOLEAN DEFAULT NULL, strip_session_id VARCHAR(255) NOT NULL, CONSTRAINT FK_22DE8175CB708DA2 FOREIGN KEY (from_order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_22DE8175CB708DA2 ON payment_request (from_order_id)');
        $this->addSql('DROP TABLE user_address');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_address (user_id INTEGER NOT NULL, address_id INTEGER NOT NULL, PRIMARY KEY(user_id, address_id), CONSTRAINT FK_5543718BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5543718BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5543718BF5B7AF75 ON user_address (address_id)');
        $this->addSql('CREATE INDEX IDX_5543718BA76ED395 ON user_address (user_id)');
        $this->addSql('DROP TABLE payment_request');
    }
}
