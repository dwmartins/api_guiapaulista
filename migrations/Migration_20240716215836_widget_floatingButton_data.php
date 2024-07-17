<?php

use App\Class\Widget;
use App\Models\Database;

class Migration_20240716215836_widget_floatingButton_data extends Database{
    protected $db;

    public function __construct() {
        try {
            $this->db = self::getConnection();
        } catch (PDOException $e) {
            showAlertLog("ERROR: ". $e->getMessage());
            throw $e;
        }
    }

    public function up() {
        // Migration implementation (up)
        try {
            $widget = new Widget();
            $widget->setWidgetName("floatingButton");

            $data = [
                'active' => false,
                'useBasicInformationPhone' => false,
                'position' => 'right',
                'phone' => ''
            ];

            $widget->setData($data);
            $widget->save();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function down() {
        // Migration implementation (rollback)
    }
}
