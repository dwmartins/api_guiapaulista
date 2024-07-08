<?php

namespace App\Class;

use App\Models\UserRecoverPasswordDAO;

class UserRecoverPassword {
    private int $id = 0;
    private int $user_id = 0;
    private string $token = "";
    private string $used = "";
    private string $expiration = "";
    private string $createdAt = "";
    private string $updatedAt = "";

    public function __construct(array $recover = null) {
        if (!empty($recover)) {
            foreach ($recover as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function toArray(): array {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'token'       => $this->token,
            'used'       => $this->used,
            'expiration' => $this->expiration,
            'createdAt'  => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void {
        $this->user_id = $user_id;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function setToken(string $token): void {
        $this->token = $token;
    }

    public function getUsed(): string {
        return $this->used;
    }

    public function setUsed(string $used): void {
        $this->used = $used;
    }

    public function getExpiration(): string {
        return $this->expiration;
    }

    public function setExpiration(string $expiration): void {
        $this->expiration = $expiration;
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

    public function save(): int {
        if(empty($this->getId())) {
            return UserRecoverPasswordDAO::save($this);
        } else {
            return UserRecoverPasswordDAO::update($this);
        }
    }

    public function fetchByTokenAndUser(): array {
        $codeInfo = UserRecoverPasswordDAO::fetchByTokenAndUser($this);

        foreach ($codeInfo as $key => $value) {
            if(empty($value)) {
                continue;
            }
            
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $codeInfo;
    }
}
