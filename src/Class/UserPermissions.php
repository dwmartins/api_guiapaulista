<?php

namespace App\Class;

use App\Models\UserPermissionsDAO;

class UserPermissions {
    private int $id;
    private int $user_id;
    private string $toUsers;
    private string $toPages;
    private string $toProducts;
    private string $toConfigsEmail;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $permission = null) {
        $this->id             = $permission['id'] ?? 0;
        $this->user_id        = $permission['user_id'] ?? 0;
        $this->toUsers        = json_encode($permission['toUsers']);
        $this->toPages        = json_encode($permission['toPages']);
        $this->toProducts     = json_encode($permission['toProducts']);
        $this->toConfigsEmail = json_encode($permission['toConfigsEmail']);
        $this->createdAt      = $permission['createdAt'] ?? '';
        $this->updatedAt      = $permission['updatedAt'] ?? '';
    }

    public function toArray(): array {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'toUsers'        => $this->toUsers,
            'toPages'        => $this->toPages,
            'toProducts'     => $this->toProducts,
            'toConfigsEmail' => $this->toConfigsEmail,
            'createdAt'      => $this->createdAt,
            'updatedAt'      => $this->updatedAt
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

    public function getToUsers(): string {
        return json_decode($this->toUsers);
    }

    public function setToUsers(string $toUsers): void {
        $this->toUsers = json_encode($toUsers);
    }

    public function getToPages(): string {
        return json_decode($this->toPages);
    }

    public function setToPages(string $toPages): void {
        $this->toPages = json_encode($toPages);
    }

    public function getToProducts(): string {
        return json_decode($this->toProducts);
    }

    public function setToProducts(string $toProducts): void {
        $this->toProducts = json_encode($toProducts);
    }

    public function getToConfigsEmail(): string {
        return json_decode($this->toConfigsEmail);
    }

    public function setToConfigsEmail(string $toConfigsEmail): void {
        $this->toConfigsEmail = json_encode($toConfigsEmail);
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

    public function save() {
        UserPermissionsDAO::save($this);
    }
}
