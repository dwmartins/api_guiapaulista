<?php

namespace App\Commands;

use App\Models\MigrationManager;

class MigrateCommand {
    public static function execute($migrationType, $argv = null) {
        $migrationManager = new MigrationManager();
        $environment = "";

        switch (DEV_MODE) {
            case true:
                $environment = "DEVELOPMENT";
                break;

            case false;
                $environment = "PRODUCTION";
                break;
            default:
                exit();
                break;
        }

        showAlertLog("Attention, we are in $environment mode");

        if($migrationType === "migrate") {
            showLog("Do you want to continue with the migration? yes/no");

            $continue = rtrim(fgets(STDIN));
            echo PHP_EOL;

            if($continue !== "yes") {
                showAlertLog("Migration closed by user.");
                exit();
            }

            $migrationManager->migrate();

        } else if($migrationType === "rollback"){
            $order = null;
            $whichMigration = null;

            //If there is no additional command, the last migration will be rolled back.
            if(strpos($argv, '--order:') === 0) {
                // Make sure you have the "--order:" attribute to revert the most recent migrations according to "--order:"
                $order = (int) substr($argv, strlen('--order:'));

                if(!$order) {
                    showLog("Command not found!");
                    showLog("Ex: php console.php rollback --order:5");
                    exit();
                }

                showLog("You are about to revert the last ($order) migrations");

            } elseif (strpos($argv, '--name:') === 0) {
                // Checks if you passed the "--name:" attribute to revert a specific migration
                $whichMigration = substr($argv, strlen('--name:'));

                if(!$whichMigration) {
                    showLog("Command not found!");
                    showLog("Ex: php console.php rollback --name:Table_users_20240622162343.php");
                    exit();
                }

                showLog("You are about to roll back the ($whichMigration) migration");
            } else {
                showLog("You are about to revert the last migration");
            }

            showLog("Do you want to continue with the rollback? yes/no");

            $continue = rtrim(fgets(STDIN));
            echo PHP_EOL;

            if($continue !== "yes") {
                showAlertLog("Rollback closed by user.");
                exit();
            }

            $migrationManager->rollback($whichMigration, $order ?? 1);
        }
    }
}
