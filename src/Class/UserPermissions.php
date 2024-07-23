<?php

namespace App\Class;

use App\Models\UserPermissionsDAO;

class UserPermissions {
    private int $id = 0;
    private int $user_id = 0;
    private string $users = "";
    private string $content = "";
    private string $siteInfo = "";
    private string $emailSending = "";
    private string $createdAt = "";
    private string $updatedAt = "";

    public function __construct(array $permission = null) {
        $this->id           = $permission['id'] ?? 0;
        $this->user_id      = $permission['user_id'] ?? 0;
        $this->createdAt    = $permission['createdAt'] ?? '';
        $this->updatedAt    = $permission['updatedAt'] ?? '';

        if (!empty($permission)) {
            $this->setPermissions($permission);
        }
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

    public function setPermissions(array $permission) {
        if (isset($permission['users'])) {
            $this->users = is_string($permission['users']) ? $permission['users'] : json_encode($permission['users']);
        }

        if (isset($permission['content'])) {
            $this->content = is_string($permission['content']) ? $permission['content'] : json_encode($permission['content']);
        }

        if (isset($permission['siteInfo'])) {
            $this->siteInfo = is_string($permission['siteInfo']) ? $permission['siteInfo'] : json_encode($permission['siteInfo']);
        }

        if (isset($permission['emailSending'])) {
            $this->emailSending = is_string($permission['emailSending']) ? $permission['emailSending'] : json_encode($permission['emailSending']);
        }
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

    public function update(array $permissions) {
        if (!empty($permissions)) {
            $this->setPermissions($permissions);
        }
    }

    public function save() {
        if(empty($this->getId())) {
            UserPermissionsDAO::save($this);
        } else {
            UserPermissionsDAO::update($this);
        }
    }

    public function getPermissions(User $user) {
        $userPermissions = UserPermissionsDAO::getPermissions($user);

        if(!empty($user)) {
            foreach ($userPermissions as $key => $value) {
                if(empty($value)) {
                    continue;
                }

                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }

        return $userPermissions;
    }
}
