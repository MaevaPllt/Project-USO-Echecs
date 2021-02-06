<?php

namespace App\Model;

class NewsManager extends AbstractManager
{
    public const TABLE = 'news';
    public const LIMIT = 3;

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * @param array $news
     */
    public function addNews(array $news)
    {
        $statement = $this->pdo->prepare('INSERT INTO ' . self::TABLE . ' (title, excerpt, content, cover_image, `date`)
        VALUES (:title, :excerpt, :content, :cover_image, NOW())');
        $statement->bindValue(':title', $news['title'], \PDO::PARAM_STR);
        $statement->bindValue(':excerpt', $news['excerpt'], \PDO::PARAM_STR);
        $statement->bindValue(':content', $news['content'], \PDO::PARAM_STR);
        $statement->bindValue(':cover_image', $news['cover_image']);
        $statement->execute();
    }

    /**
     * @param int $id
     */
    public function deleteNews(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function updateNews(array $news)
    {
        $query = ('UPDATE ' . self::TABLE .
            ' SET title = :title, excerpt = :excerpt, content = :content, cover_image = :cover_image 
            WHERE id = :id');
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':title', $news['title'], \PDO::PARAM_STR);
        $statement->bindValue(':excerpt', $news['excerpt'], \PDO::PARAM_STR);
        $statement->bindValue(':content', $news['content'], \PDO::PARAM_STR);
        $statement->bindValue(':cover_image', $news['cover_image']);
        $statement->bindValue('id', $news['id'], \PDO::PARAM_INT);
        $statement->execute();
    }

    public function latestNews(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table .
            ' ORDER BY date DESC LIMIT ' . self::LIMIT)->fetchAll();
    }
}
