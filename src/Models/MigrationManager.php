<?php

namespace App\Models;

use PDO;
use PDOException;

class MigrationManager extends Database{
    protected $db;

    public function __construct() {
        try {
            $this->db = self::getConnection();
        } catch (PDOException $e) {
            showAlertLog("ERROR: ". $e->getMessage());
            logError($e->getMessage());
            throw $e;
        }
    }

    public function migrate() {
        try {
            $this->createMigrationsTableIfNotExists();
            $appliedMigrations = $this->getAppliedMigrations();
            $migrationsFiles = scandir(__DIR__ . '/../../migrations');
            $migrationsToApply = array_diff($migrationsFiles, ['.', '..', '.gitignore']);
            $pendingMigrations = array_diff($migrationsToApply, $appliedMigrations);

            if(empty($pendingMigrations)) {
                showLog("No pending migrations to apply.");
                return;
            }

            foreach ($pendingMigrations as $migrationFile) {
                require_once __DIR__ . '/../../migrations/' . $migrationFile;
                showLog("Running migration to $migrationFile", true);
                $className = pathinfo($migrationFile, PATHINFO_FILENAME);
                $migration = new $className();
                $migration->up();

                $this->markMigrationApplied($migrationFile);
            }

            showSuccessLog("Migrations have been executed successfully.");
        } catch (PDOException $e) {
            showAlertLog("ERROR: ". $e->getMessage());
            logError($e->getMessage());
        }
    }

    public function rollback($whichMigration = null, $order = 1) {
        try {
            if($whichMigration) {
                $migrationApplied = $this->getMigration($whichMigration);
                $migrationFile = __DIR__ . '/../../Migrations/' . $whichMigration;

                if($migrationApplied && file_exists($migrationFile)) {
                    require_once __DIR__ . '/../../migrations/' . $whichMigration;
                    $className = pathinfo($whichMigration, PATHINFO_FILENAME);
                    $migration = new $className();
                    $migration->down();
                    $this->removeMigrationRecord($whichMigration);
                    showLog("Running the rollback to $migrationApplied", true);
                    showSuccessLog("Rollback executed successfully");

                    // Delete the rollback migration
                    $filePatch = __DIR__ . '/../../migrations/' . $whichMigration;
                    if(file_exists($filePatch)) {
                        unlink($filePatch);
                    }

                    return;
                } else {
                    showAlertLog("The migration ($whichMigration) was not found.");
                    return;
                }
            }

            $appliedMigrations = $this->getAppliedMigrations();
            $migrationsToRollback = array_reverse($appliedMigrations);

            $count = 0;
            foreach ($migrationsToRollback as $migrationFile) {
                if ($count >= $order) {
                    break; // Stop the loop if it reaches the desired number of rollback migrations
                }

                require_once __DIR__ . '/../../migrations/' . $migrationFile;
                $className = pathinfo($migrationFile, PATHINFO_FILENAME);
                $migration = new $className();
                $migration->down();
                $count++;
                $this->removeMigrationRecord($migrationFile);
                showLog("Running the rollback to $migrationFile", true);

                // Delete the rollback migration
                $filePatch = __DIR__ . '/../../migrations/' . $migrationFile;
                if(file_exists($filePatch)) {
                    unlink($filePatch);
                }
            }
    
            showSuccessLog("Rollback executed successfully");
        } catch (PDOException $e) {
            showAlertLog("ERROR: ". $e->getMessage());
            logError($e->getMessage());
        }
    }

    protected function createMigrationsTableIfNotExists() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    protected function getAppliedMigrations() {
        try {
            $stmt = $this->db->prepare("SELECT migration FROM migrations");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function getMigration($migrationFile) {
        try {
            $stmt = $this->db->prepare("SELECT migration FROM migrations WHERE migration = :migration");
            $stmt->bindValue(':migration', $migrationFile);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function markMigrationApplied($migrationFile) {
        try {
            $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
            $stmt->bindValue(':migration', $migrationFile);
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function removeMigrationRecord($migrationFile) {
        try {
            $stmt = $this->db->prepare("DELETE FROM migrations WHERE migration = :migration");
            $stmt->bindValue(':migration', $migrationFile);
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
}