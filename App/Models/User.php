<?php

namespace App\Models;

use PDO;

class User extends \Core\Model
{
    public function __construct($data)
    {
        foreach ($data as $key => $value){
            $this->$key = $value;
        };
    }

    public function save()
    {
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)";
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);

        $stmt->execute();


    }

    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query("SELECT id,name FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}