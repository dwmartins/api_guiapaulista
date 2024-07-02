<?php

namespace App\Class;

use App\Models\UserRecoverPasswordDAO;

class UserRecoverPassword {
    private int $id;
    private int $user_id;
    private string $code;
    private string $used;
    private string $expiration;
    private string $createdAt;

    public function __construct(array $recover = null) {
        $this->id = $recover['id'] ?? 0;
        $this->user_id = $recover['user_id'] ?? 0;
        $this->code = $recover['code'] ?? '';
        $this->used = $recover['used'] ?? 'Y';
        $this->expiration = $recover['expiration'] ?? '';
        $this->createdAt = $recover['createdAt'] ?? '';
    }

    public function toArray(): array {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'code'       => $this->code,
            'used'       => $this->used,
            'expiration' => $this->expiration,
            'createdAt'  => $this->createdAt,
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

    public function getCode(): string {
        return $this->code;
    }

    public function setCode(string $code): void {
        $this->code = $code;
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

    public function save(): int {
        return UserRecoverPasswordDAO::save($this);
    }
}
