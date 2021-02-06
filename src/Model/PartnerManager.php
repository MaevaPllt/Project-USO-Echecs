<?php

namespace App\Model;

class PartnerManager extends AbstractManager
{
    /**
     *
     */
    public const TABLE = 'partner';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectAll(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table . ' ORDER BY name')->fetchAll();
    }

    public function addPartner(array $partner)
    {
        $query = ("INSERT INTO " . self::TABLE . "(name, url, image) VALUES (:name,:url, :image)");
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':name', $partner['name'], \PDO::PARAM_STR);
        $statement->bindValue(':url', $partner['url'], \PDO::PARAM_STR);
        $statement->bindValue(':image', $partner['image']);
        $statement->execute();
    }

    public function updatepartner(array $partner)
    {
        $query = ('UPDATE ' . self::TABLE . ' SET name = :name, url = :url, image = :image WHERE id = :id');
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $partner['id'], \PDO::PARAM_INT);
        $statement->bindValue(':name', $partner['name'], \PDO::PARAM_STR);
        $statement->bindValue(':url', $partner['url'], \PDO::PARAM_STR);
        $statement->bindValue(':image', $partner['image']);
        $statement->execute();
    }

    public function delete(int $id)
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
