<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250804204712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_loyalty_reward (user_id INT NOT NULL, loyalty_reward_id INT NOT NULL, INDEX IDX_E229F35EA76ED395 (user_id), INDEX IDX_E229F35E855F5751 (loyalty_reward_id), PRIMARY KEY(user_id, loyalty_reward_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_loyalty_reward ADD CONSTRAINT FK_E229F35EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_loyalty_reward ADD CONSTRAINT FK_E229F35E855F5751 FOREIGN KEY (loyalty_reward_id) REFERENCES loyalty_reward (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP loyalty_rewards');

        $this->addSql("INSERT INTO loyalty_reward (reward) VALUES ('{\"name\": \"Free Drink\"}'), ('{\"name\": \"5% Off\"}'), ('{\"name\": \"VIP Access\"}')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_loyalty_reward DROP FOREIGN KEY FK_E229F35EA76ED395');
        $this->addSql('ALTER TABLE user_loyalty_reward DROP FOREIGN KEY FK_E229F35E855F5751');
        $this->addSql('DROP TABLE user_loyalty_reward');
        $this->addSql('ALTER TABLE user ADD loyalty_rewards JSON DEFAULT NULL');
    }
}
