<?php

/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

class AdminCompetitionManager extends AbstractManager
{
    /**
     *
     */
    public const TABLE = 'competition';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /***
     * @param array $item
     */
    public function add(array $item): void
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
        (name, `date`, address, description, picture)
        VALUES (:name, :date, :address, :description, :picture)");
        $statement->bindValue('name', $item['name'], \PDO::PARAM_STR);
        $statement->bindValue('date', $item['date']);
        $statement->bindValue('description', $item['description'], \PDO::PARAM_STR);
        $statement->bindValue('address', $item['address'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $item['picture']);
        $statement->execute();
    }
}
