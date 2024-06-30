<?php

namespace App\Class;

use App\Models\UserPermissionsDAO;

class UserPermissions {
    private int $id;
    private int $user_id;
    private string $users;
    private string $content;
    private string $siteInfo;
    private string $emailSending;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $permission = null) {
        $this->id           = $permission['id'] ?? 0;
        $this->user_id      = $permission['user_id'] ?? 0;
        $this->users        = !is_string($permission['users']) ? json_encode($permission['users']) : $permission['users'];
        $this->content      = !is_string($permission['content']) ? json_encode($permission['content']) : $permission['content'];
        $this->siteInfo     = !is_string($permission['siteInfo']) ? json_encode($permission['siteInfo']) : $permission['siteInfo'];
        $this->emailSending = !is_string($permission['emailSending']) ? json_encode($permission['emailSending']) : $permission['emailSending'];
        $this->createdAt    = $permission['createdAt'] ?? '';
        $this->updatedAt    = $permission['updatedAt'] ?? '';
    }

    public function toArray(): array {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'users'          => $this->users,
            'content'        => $this->content,
            'siteInfo'       => $this->siteInfo,
            'emailSending'   => $this->emailSending,
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

    public function getUsers(): array {
        return json_decode($this->users, true);
    }

    public function setUsers(string $users): void {
        $this->users = json_encode($users);
    }

    public function getContent(): array {
        return json_decode($this->content, true);
    }

    public function setContent(string $content): void {
        $this->content = json_encode($content);
    }

    public function getSiteInfo(): array {
        return json_decode($this->siteInfo, true);
    }

    public function setSiteInfo(string $siteInfo): void {
        $this->siteInfo = json_encode($siteInfo);
    }

    public function getEmailSending(): array {
        return json_decode($this->emailSending, true);
    }

    public function setEmailSending(string $emailSending): void {
        $this->emailSending = json_encode($emailSending);
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
