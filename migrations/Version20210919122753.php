<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210919122753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, trick_id INT NOT NULL, content LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526CB281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contribution (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, trick_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, lead_in LONGTEXT NOT NULL, content LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_EA351E15A76ED395 (user_id), INDEX IDX_EA351E1512469DE2 (category_id), INDEX IDX_EA351E15B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, trick_id INT DEFAULT NULL, contribution_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, original_file_name VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, in_front INT DEFAULT NULL, image_target INT DEFAULT NULL, INDEX IDX_C53D045FB281BE2E (trick_id), INDEX IDX_C53D045FFE5E5FBD (contribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, lead_in LONGTEXT NOT NULL, content LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, modified_date DATETIME DEFAULT NULL, INDEX IDX_D8F0A91E12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D64986CC499D (pseudo), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_trick (user_id INT NOT NULL, trick_id INT NOT NULL, INDEX IDX_3A325246A76ED395 (user_id), INDEX IDX_3A325246B281BE2E (trick_id), PRIMARY KEY(user_id, trick_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, trick_id INT DEFAULT NULL, contribution_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, video_target INT DEFAULT NULL, INDEX IDX_7CC7DA2CB281BE2E (trick_id), INDEX IDX_7CC7DA2CFE5E5FBD (contribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE contribution ADD CONSTRAINT FK_EA351E15A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contribution ADD CONSTRAINT FK_EA351E1512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE contribution ADD CONSTRAINT FK_EA351E15B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE5E5FBD FOREIGN KEY (contribution_id) REFERENCES contribution (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user_trick ADD CONSTRAINT FK_3A325246A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_trick ADD CONSTRAINT FK_3A325246B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CFE5E5FBD FOREIGN KEY (contribution_id) REFERENCES contribution (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contribution DROP FOREIGN KEY FK_EA351E1512469DE2');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E12469DE2');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE5E5FBD');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CFE5E5FBD');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB281BE2E');
        $this->addSql('ALTER TABLE contribution DROP FOREIGN KEY FK_EA351E15B281BE2E');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FB281BE2E');
        $this->addSql('ALTER TABLE user_trick DROP FOREIGN KEY FK_3A325246B281BE2E');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CB281BE2E');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE contribution DROP FOREIGN KEY FK_EA351E15A76ED395');
        $this->addSql('ALTER TABLE user_trick DROP FOREIGN KEY FK_3A325246A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contribution');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE trick');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_trick');
        $this->addSql('DROP TABLE video');
    }
}
