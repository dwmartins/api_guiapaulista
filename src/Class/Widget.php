<?php

namespace App\Class;

use App\Models\WidgetDAO;

class Widget {
    private int $id = 0;
    private string $widget_name = "";
    private string $widget_data = "";
    private string $createdAt = "";
    private string $updatedAt = "";

    public function __construct(array $widget = null) {
        if(!empty($widget)) {
            $this->id = $widget['id'] ?? 0;
            $this->widget_name = $widget['widget_name'] ?? "";
    
            if(isset($widget['widget_data'])) {
                $this->widget_data = is_string($widget['widget_data']) ? $widget['widget_data'] : json_encode($widget['widget_data']);
            }
    
            $this->createdAt = $widget['createdAt'] ?? "";
            $this->updatedAt = $widget['updatedAt'] ?? "";
        }
    }

    public function toArray(): array {
        return [
            'id'          => $this->id,
            'widget_name' => $this->widget_name,
            'widget_data' => $this->widget_data,
            'createdAt'   => $this->createdAt,
            'updatedAt'   => $this->updatedAt
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public function getWidgetName() {
        return $this->widget_name;
    }

    public function setWidgetName(string $widget_name) {
        $this->$widget_name = $widget_name;
    }

    public function getData(): array {
        return json_decode($this->widget_data);
    }

    public function setData(array $widget_data): void {
        $this->$widget_data = json_encode($widget_data);
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
        if(empty($this->getId())) {
            WidgetDAO::save($this);
        } else {
            WidgetDAO::update($this);
        }
    }

    public function getWidgetById(string $widgetName) {
        $widget = WidgetDAO::fetchById($widgetName);

        if(!empty($widget)) {
            $this->widget_name = $widget['widget_name'];
            $this->widget_data = json_decode($widget['widget_data']);
            $this->createdAt = $widget['createdAt'];
            $this->updatedAt = $widget['updatedAt'];

            return $this->toArray();
        }

        return [];
    }

    public function getWidgets() {
        return WidgetDAO::fetchAll($this);
    }
}
