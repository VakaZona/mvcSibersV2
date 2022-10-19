<?php

namespace App\Models;

use PDO;

class User extends \Core\Model
{
    public $errors = [];
    public function __construct($data = [])
    {
        foreach ($data as $key => $value){
            $this->$key = $value;
        };
    }

    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = 'INSERT INTO users (`name`, email, password_hash) VALUES (:name, :email, :password_hash)';
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query("SELECT id,name FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validate()
    {
        if ($this->name == ''){
            $this->errors[] = 'Name is required'; // Не актульно, решено через HTML
        }
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Invalid email';
        }
        if ($this->emailExists($this->email)){
            $this->errors[] = 'email already taken';
        }
        if ($this->password != $this->password_confirmation){
            $this->errors[] = 'Password must match confirmation';
        }
        if (strlen($this->password) < 6){
            $this->errors[] = 'Please enter at least 6 characters for the password';
        }
        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0){
            $this->errors[] = 'Password needs at least one letter';
        }
        if (preg_match('/.*\d+.*/i', $this->password) == 0){
            $this->errors[] = 'Password needs at least one number';
        }
    }

    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email= :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if($user){
            if (password_verify($password, $user->password_hash)){
                return $user;
            }
        }
        return false;
    }
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id= :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
}
