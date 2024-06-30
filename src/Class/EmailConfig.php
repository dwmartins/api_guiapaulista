<?php

namespace App\Class;

use App\Models\EmailConfigDAO;

class EmailConfig {
    private int $id;
    private string $server;
    private string $emailAddress;
    private string $username;
    private string $password;
    private int $port;
    private string $authentication;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $emailConfig = null) {
        $this->server         = $emailConfig['server'] ?? '';
        $this->emailAddress   = $emailConfig['emailAddress'] ?? '';
        $this->username       = $emailConfig['username'] ?? '';
        $this->password       = $emailConfig['password'] ?? '';
        $this->port           = $emailConfig['port'] ?? '';
        $this->authentication = $emailConfig['authentication'] ?? '';
        $this->createdAt      = $emailConfig['createdAt'] ?? '';
        $this->updatedAt      = $emailConfig['updatedAt'] ?? '';
    }

    public function toArray(): array {
        return [
            'id'             => $this->id,
            'server'         => $this->server,
            'emailAddress'   => $this->emailAddress,
            'username'       => $this->username,
            'password'       => $this->password,
            'port'           => $this->port,
            'authentication' => $this->authentication,
            'createdAt'      => $this->createdAt,
            'updatedAt'      => $this->updatedAt,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getServer(): string {
        return $this->server;
    }

    public function setServer(string $server): void {
        $this->server = $server;
    }

    public function getEmailAddress(): string {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void {
        $this->emailAddress = $emailAddress;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function getPort(): int {
        return $this->port;
    }

    public function setPort(string $port): void {
        $this->port = $port;
    }

    public function getAuthentication(): string {
        return $this->authentication;
    }

    public function setAuthentication(string $authentication): void {
        $this->authentication = $authentication;
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
        EmailConfigDAO::save($this);
    }
}
