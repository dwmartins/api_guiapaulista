<?php 

namespace App\Class;

use App\Models\UserDAO;

class User {
    public $id;
    public $name;
    public $lastName;
    public $email;
    public $password;
    public $token;
    public $active;
    public $role;
    public $photo;
    public $createdAt;
    public $updatedAt;

    public function __construct($user) {
        $this->id        = $user['id'] ?? '';
        $this->name      = $user['name'];
        $this->lastName  = $user['lastName'];
        $this->email     = $user['email'];
        $this->password  = $user['password'] ?? '';
        $this->token     = $user['token'] ?? '';
        $this->active    = $user['active'] ?? '';
        $this->role      = $user['role'] ?? '';
        $this->photo     = $user['photo'] ?? '';
        $this->createdAt = $user['createdAt'] ?? '';
        $this->updatedAt = $user['updatedAt'] ?? '';
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        return $this->token = $token;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($photo) {
        $this->photo = $photo;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function save() {
        return UserDAO::save($this);
    }
}