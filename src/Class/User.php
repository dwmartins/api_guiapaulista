<?php 

namespace App\Class;

use App\Models\UserDAO;

class User {
    private int $id = 0;
    private string $name = "";
    private string $lastName = "";
    private string $email = "";
    private string $password = "";
    private string $token = "";
    private string $active = "N";
    private string $role = "mod";
    private string $photo = "";
    private int $createdBy = 0;
    private string $createdAt = "";
    private string $updatedAt = "";

    public function __construct(array $user = null) {
        if (!empty($user)) {
            foreach ($user as $key => $value) {
                if (property_exists($this, $key)) {

                    if($key === "password") {
                        $this->password = $this->isPasswordHashed($value) ? $value : password_hash($value, PASSWORD_DEFAULT);
                        continue;
                    }

                    $this->$key = $value;
                }
            }
        }
    }

    public function toArray(): array {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'lastName'  => $this->lastName,
            'email'     => $this->email,
            'password'  => $this->password,
            'token'     => $this->token,
            'active'    => $this->active,
            'role'      => $this->role,
            'photo'     => $this->photo,
            'createdBy' => $this->createdBy,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = ucfirst($name);
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = ucfirst($lastName);
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getToken(): string {
        return $this->token;
    }

    public function setToken(string $token): void {
        $this->token = $token;
    }

    public function getActive(): string {
        return $this->active;
    }

    public function setActive(string $active): void {
        $this->active = $active;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function getPhoto(): string {
        return $this->photo;
    }

    public function setPhoto(string $photo): void {
        $this->photo = $photo;
    }

    public function getCreatedBy(): int {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): void {
        $this->createdBy = $createdBy;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    private function isPasswordHashed($password) {
        $info = password_get_info($password);
        return $info['algo'];
    }

    public function update(array $user): void {
        foreach ($user as $key => $value) {
            if(empty($value)) {
                continue;
            }

            if (property_exists($this, $key)) {
                if($key === "password") {
                    $this->password = $this->isPasswordHashed($value) ? $value : password_hash($value, PASSWORD_DEFAULT);
                    continue;
                }

                $this->$key = $value;
            }
        }
    }

    public function save(): void {
        if(empty($this->getId())) {
            $this->id = UserDAO::save($this);
        } else {
            UserDAO::update($this);
        }
    }

    public function updatePassword(): int {
        return UserDAO::updatePassword($this);
    }

    public function updatePhoto(): int {
        return UserDAO::updatePhoto($this);
    }

    public function delete(): int {
        return UserDAO::delete($this);
    }

    public function fetchById(int $user_id): array {
        $user = UserDAO::fetchById($user_id);

        if(!empty($user)) {
            foreach ($user as $key => $value) {
                if(empty($value)) {
                    continue;
                }

                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }

        return $user;
    }

    public function fetchByEmail(string $email): array {
        $user = UserDAO::fetchByEmail($email);

        if(!empty($user)) {
            foreach ($user as $key => $value) {
                if(empty($value)) {
                    continue;
                }

                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }

        return $user;
    }

    public function updateRole(): int {
        return UserDAO::updateRole($this);
    }
 }