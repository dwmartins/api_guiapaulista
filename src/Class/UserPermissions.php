<?php

namespace App\Class;

use App\Models\UserPermissionsDAO;

class UserPermissions {
    private int $id;
    private int $user_id;
    private string $users;
    private string $content;
    private string $settings;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $permission = null) {
        $this->id           = $permission['id'] ?? 0;
        $this->user_id      = $permission['user_id'] ?? 0;
        $this->users        = json_encode($permission['users']);
        $this->content      = json_encode($permission['content']);
        $this->settings     = json_encode($permission['settings']);
        $this->createdAt    = $permission['createdAt'] ?? '';
        $this->updatedAt    = $permission['updatedAt'] ?? '';
    }

    public function toArray(): array {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'users'          => $this->users,
            'content'        => $this->content,
            'settings'       => $this->settings,
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

    public function getUsers(): string {
        return json_decode($this->users);
    }

    public function setUsers(string $users): void {
        $this->users = json_encode($users);
    }

    public function getContent(): string {
        return json_decode($this->content);
    }

    public function setContent(string $content): void {
        $this->content = json_encode($content);
    }

    public function getSettings(): string {
        return json_decode($this->settings);
    }

    public function setSettings(string $settings): void {
        $this->settings = json_encode($settings);
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
