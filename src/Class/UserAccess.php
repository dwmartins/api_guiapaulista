<?php

namespace App\Class;

use App\Models\UserAccessDAO;

class UserAccess {
    public int $id;
    public int $user_id;
    public string $ip;
    public string $createdAt;

    public function __construct(array $access = null) {
        $this->id        = $access['id'] ?? 0;
        $this->user_id   = $access['user_id'] ?? 0;
        $this->ip        = $access['ip'] ?? '';
        $this->createdAt = $access['createdAt'] ?? '';
    }

    public function toArray(): array {
        return [
            'id'        => $this->id,
            'user_id'   => $this->user_id,
            'ip'        => $this->ip,
            'createdAt' => $this->createdAt,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void {
        $this->user_id = $user_id;
    }

    public function getIp(): int  {
        return $this->ip;
    }

    public function setIp(string $ip): void {
        $this->ip = $ip;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function save(): int {
       return UserAccessDAO::save($this);
    }
}