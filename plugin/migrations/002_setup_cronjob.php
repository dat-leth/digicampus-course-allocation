<?php
require_once __DIR__ . '/../cronjobs/BundleAllocationAdmissionJob.class.php';

class SetupCronjob extends Migration
{
    /**
     *
     */
    function up()
    {
        DBManager::get()->execute("INSERT INTO `studip`.`config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`) 
            VALUES ('BUNDLEALLOCATION_SERVER_ENDPOINT', 
                    'https://localhost:5000/', 
                    'string', 
                    'global', 
                    'bundleallocationplugin', 
                    UNIX_TIMESTAMP(), 
                    UNIX_TIMESTAMP(), 
                    'Endpunkt-URL des BundleAllocation-Verteil-Service')"
        );
        BundleAllocationAdmissionJob::register()->schedulePeriodic(-30)->activate();
    }

    /**
     *
     */
    function down()
    {
        DBManager::get()->execute("DELETE FROM `studip`.`config` WHERE `field` = 'BUNDLEALLOCATION_SERVER_ENDPOINT';");
        BundleAllocationAdmissionJob::unregister();
    }

}