<?php

namespace App\Models;

use App\Config;
use App\Token;
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
    public function update()
    {
        $this->validateUpdate();

        if (empty($this->errors)){
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name=:name, password_hash=:password_hash, email=:email WHERE id=:id";
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public static function getAll($sortFlag = [], $page, $kol)
    {
        $art = ($page * $kol) - $kol;
        if (empty($sortFlag)){
            $db = static::getDB();
            $stmt = $db->query("SELECT * FROM users ORDER BY id LIMIT $art,$kol");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            switch ($sortFlag){
                case 'id_ASC':
                    $sort = 'id ASC';
                    break;
                case 'id_DESC':
                    $sort = 'id DESC';
                    break;
                case 'name_ASC':
                    $sort = 'name ASC';
                    break;
                case 'name_DESC':
                    $sort = 'name DESC';
                    break;
                default:
                    $sort = 'id ASC';
            }
            $db = static::getDB();
            $stmt = $db->query("SELECT * FROM users ORDER BY $sort LIMIT $art,$kol");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    }
    public function validateUpdate()
    {
        if ($this->name == ''){
            $this->errors[] = 'Name is required'; // Не актульно, решено через HTML
        }
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Invalid email';
        }
        // Не актуально для Update
        /*
        if ($this->emailExists($this->email)){
            $this->errors[] = 'email already taken';
        }*/

        if((static::checkNewEmail($this->email, $this->id)) == 0){
            if ($this->emailExists($this->email)) {
                $this->errors[] = 'email already taken';
            }
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
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60*60*24*30;

        $sql = "INSERT INTO remembererd_logins (token_hash, user_id, expires_at) VALUES (:token_hash, :user_id, :expires_at)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s',$this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
    public static function deleteUser($id)
    {
        $db = static::getDB();
        $sql = "DELETE FROM users WHERE id=:id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public static function countRowInDb()
    {
        $db = static::getDB();
        $sql = "SELECT COUNT(*) FROM users";
        $stmt = $db->query($sql);
        $count = $stmt->fetchColumn();
        $stmt->execute();
        return $count;
    }
    public static function checkNewEmail($email, $id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM users WHERE id='$id' AND email='$email'";
        $stmt = $db->query($sql);
        $countEmail = $stmt->fetchColumn();
        $stmt->execute();
        return $countEmail;
    }
}
