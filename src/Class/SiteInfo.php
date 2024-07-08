<?php

namespace App\Class;

use App\Models\SiteInfoDAO;

class SiteInfo {
    private int $id = 0;
    private string $webSiteName = "";
    private string $email = "";
    private string $phone = "";
    private string $city = "";
    private string $state = "";
    private string $address = "";
    private string $workSchedule = "";
    private string $instagram = "";
    private string $facebook = "";
    private string $description = "";
    private string $keywords = "";
    private string $ico = "";
    private string $logoImage = "";
    private string $coverImage = "";
    private string $defaultImage = "";
    private string $createdAt = "";
    private string $updatedAt = "";

    public function __construct(array $siteInfo = null) {
        if (!empty($siteInfo)) {
            foreach ($siteInfo as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function toArray(): array {
        return [
            'id'           => $this->id,
            'webSiteName'  => $this->webSiteName,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'city'         => $this->city,
            'state'        => $this->state,
            'address'      => $this->address,
            'workSchedule' => $this->workSchedule,
            'instagram'    => $this->instagram,
            'facebook'     => $this->facebook,
            'description'  => $this->description,
            'keywords'     => $this->keywords,
            'ico'          => $this->ico,
            'logoImage'    => $this->logoImage,
            'coverImage'   => $this->coverImage,
            'defaultImage' => $this->defaultImage,
            'createdAt'    => $this->createdAt,
            'updatedAt'    => $this->updatedAt,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getWebSiteName(): string {
        return $this->webSiteName;
    }

    public function setWebSiteName(string $webSiteName): void {
        $this->webSiteName = $webSiteName;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getPhone(): string {
        return $this->phone;
    }

    public function setPhone(string $phone): void {
        $this->phone = $phone;
    }

    public function getCity(): string {
        return $this->city;
    }

    public function setCity(string $city): void {
        $this->city = $city;
    }

    public function getState(): string {
        return $this->state;
    }

    public function setState(string $state): void {
        $this->state = $state;
    }

    public function getAddress(): string {
        return $this->address;
    }

    public function setAddress(string $address): void {
        $this->address = $address;
    }

    public function getWorkSchedule(): string {
        return $this->workSchedule;
    }

    public function setWorkSchedule(string $workSchedule): void {
        $this->workSchedule = $workSchedule;
    }

    public function getInstagram(): string {
        return $this->instagram;
    }

    public function setInstagram(string $instagram): void {
        $this->instagram = $instagram;
    }

    public function getFacebook(): string {
        return $this->facebook;
    }

    public function setFacebook(string $facebook): void {
        $this->facebook = $facebook;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getKeywords(): string {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): void {
        $this->keywords = $keywords;
    }

    public function getIco(): string {
        return $this->ico;
    }

    public function setIco(string $ico): void {
        $this->ico = $ico;
    }

    public function getLogoImage(): string {
        return $this->logoImage;
    }

    public function setLogoImage(string $logoImage): void {
        $this->logoImage = $logoImage;
    }

    public function getCoverImage(): string {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): void {
        $this->coverImage = $coverImage;
    }

    public function getDefaultImage(): string {
        return $this->defaultImage;
    }

    public function setDefaultImage(string $defaultImage): void {
        $this->defaultImage = $defaultImage;
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

    public function setUpdatedAt(string $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function save(): void {
        if(empty($this->getId())) {
            $this->id = SiteInfoDAO::save($this);
        } else {
            SiteInfoDAO::update($this);
        }
    }

    public function update(array $siteInfo): void {
        foreach ($siteInfo as $key => $value) {
            if(empty($value)) {
                continue;
            }

            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function fetch(): array {
        $siteInfo = SiteInfoDAO::fetch($this);

        if(!empty($siteInfo)) {
            foreach ($siteInfo as $key => $value) {
                if(empty($value)) {
                    continue;
                }
    
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }

        return $siteInfo;
    }
}
