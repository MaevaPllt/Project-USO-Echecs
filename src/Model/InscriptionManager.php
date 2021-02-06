<?php

/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 20:52
 * PHP version 7
 */

namespace App\Model;

use App\Model\Connection;

/**
 * Abstract class handling default manager.
 */
class InscriptionManager extends AbstractManager
{
    private const TABLE = 'registration';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectValidate()
    {
        $query = ('SELECT * FROM ' . $this->table . ' WHERE is_validate = true ' .
            ' ORDER BY status is NULL, status, lastname');
        return $this->pdo->query($query)->fetchAll();
    }

    public function selectNonValidate()
    {
        $query = ('SELECT * FROM ' . $this->table . ' WHERE is_validate = false' .
            ' ORDER BY status is NULL, status, lastname');
        return $this->pdo->query($query)->fetchAll();
    }

    public function selectStatus()
    {
        return $this->pdo->query('SELECT * FROM ' . self::TABLE . ' WHERE status IS NOT NULL')->fetchAll();
    }

    public function addMember($data)
    {
        $query = ("INSERT INTO " . self::TABLE . " 
        (firstname, lastname, email, phone, birthday, address, postal_code, city, is_validate, status) 
        VALUES (:inputFirstname,:inputLastname, :inputEmail, :inputPhone, :inputBirthday, :inputAddress, 
        :inputPostalCode, :inputCity, :is_validate, :status)");
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':inputFirstname', $data['firstname']);
        $statement->bindValue(':inputLastname', $data['lastname']);
        $statement->bindValue(':inputEmail', $data['email']);
        $statement->bindValue(':inputPhone', $data['phone']);
        $statement->bindValue(':inputBirthday', $data['birthday']);
        $statement->bindValue(':inputAddress', $data['address']);
        $statement->bindValue(':inputPostalCode', $data['postal_code']);
        $statement->bindValue(':inputCity', $data['city']);
        $statement->bindValue(':is_validate', $data['is_validate']);
        $statement->bindValue(':status', $data['status']);
        $statement->execute();
    }

    public function updateMember(array $member)
    {
        $query = ('UPDATE ' . self::TABLE . ' SET firstname = :firstname, lastname = :lastname, email = :email, ' .
            'phone = :phone, birthday = :birthday, address = :address, postal_code = :postal_code, city = :city, ' .
            'status = :status WHERE id = :id');
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $member['id'], \PDO::PARAM_INT);
        $statement->bindValue(':firstname', $member['firstname'], \PDO::PARAM_STR);
        $statement->bindValue(':lastname', $member['lastname'], \PDO::PARAM_STR);
        $statement->bindValue(':email', $member['email'], \PDO::PARAM_STR);
        $statement->bindValue(':phone', $member['phone']);
        $statement->bindValue(':birthday', $member['birthday']);
        $statement->bindValue(':address', $member['address'], \PDO::PARAM_STR);
        $statement->bindValue(':postal_code', $member['postal_code']);
        $statement->bindValue(':city', $member['city'], \PDO::PARAM_STR);
        $statement->bindValue(':status', $member['status'], \PDO::PARAM_STR);
        $statement->execute();
    }

    public function delete(int $id)
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function acceptMember(int $id)
    {
        $statement = $this->pdo->prepare("UPDATE `" . self::TABLE . "` SET is_validate = :action WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->bindValue('action', true);
        $statement->execute();
    }
}
