<?php

namespace App\Class;

use App\Models\EmailConfigDAO;

class EmailConfig {
    private int $id = 0;
    private string $server = "";
    private string $emailAddress = "";
    private string $username = "";
    private string $password = "";
    private int $port = 465;
    private string $authentication = "SSL";
    private string $activated = "N";
    private string $createdAt = "";
    private string $updatedAt = "";

    public function __construct(array $emailConfig = null) {
        if (!empty($emailConfig)) {
            foreach ($emailConfig as $key => $value) {
                if (property_exists($this, $key)) {
                    if($key === "password") {
                        if($emailConfig['password'] && !empty($emailConfig['password'])) {
                            $this->setPassword($emailConfig['password']);
                            continue;
                        }
                    }
                    
                    $this->$key = $value;
                }
            }
        }
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
            'activated'      => $this->activated,
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
        $cipher = "aes-256-cbc";
        list($encrypted_data, $iv) = explode('::', base64_decode($this->password), 2);
        return openssl_decrypt($encrypted_data, $cipher, $_ENV['ENCRYPTION_KEY_FOR_EMAIL_CONF'], 0, $iv);
    }

    public function setPassword(string $password): void {
        $cipher = "aes-256-cbc";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($password, $cipher, $_ENV['ENCRYPTION_KEY_FOR_EMAIL_CONF'], 0, $iv);

        $this->password = base64_encode($encrypted . '::' . $iv);
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

    public function getActivated(): string {
        return $this->activated;
    }

    public function setActivated(string $activated): void {
        $this->activated = $activated;
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

    public function update(array $emailConfig): void {
        foreach ($emailConfig as $key => $value) {
            if($key === "password" && !empty($emailConfig['password'])) {
                $this->setPassword($emailConfig['password']);
                continue;
            }

            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function save(): int {
        if(empty($this->getId())) {
            return EmailConfigDAO::save($this);
        } else {
            return EmailConfigDAO::update($this);
        }
    }

    public function fetch(): array {
        $emailConfig = EmailConfigDAO::fetch();

        if(!empty($emailConfig)) {
            foreach ($emailConfig as $key => $value) {
                if(empty($value)) {
                    continue;
                }
    
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }

        return $emailConfig;
    }

    public function configActive(): bool {
        return $this->getActivated() === "Y" ? true : false;
    }
}
