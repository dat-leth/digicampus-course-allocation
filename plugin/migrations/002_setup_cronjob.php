<?php
require_once __DIR__ . '/../cronjobs/BundleAllocationAdmissionJob.class.php';

class SetupCronjob extends Migration
{
    /**
     *
     */
    function up()
    {
        DBManager::get()->execute("INSERT IGNORE INTO `config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`) 
            VALUES ('BUNDLEALLOCATION_SERVER_ENDPOINT', 
                    'https://localhost:5000/', 
                    'string', 
                    'global', 
                    'UAUX_BundleAllocationPlugin', 
                    UNIX_TIMESTAMP(), 
                    UNIX_TIMESTAMP(), 
                    'Endpunkt-URL des BundleAllocation-Verteil-Service'), 
                    ('BUNDLEALLOCATION_SERVER_BEARER_TOKEN', 
                    '', 
                    'string', 
                    'global', 
                    'UAUX_BundleAllocationPlugin', 
                    UNIX_TIMESTAMP(), 
                    UNIX_TIMESTAMP(), 
                    'HTTP-Authentifiziering mittels diesem Sicherheitstoken an BundleAllocation-Verteil-Service')"
        );
        BundleAllocationAdmissionJob::register()->schedulePeriodic(-15)->activate();
    }

    /**
     *
     */
    function down()
    {
        DBManager::get()->execute("DELETE FROM `config` WHERE `field` IN ('BUNDLEALLOCATION_SERVER_ENDPOINT', 'BUNDLEALLOCATION_SERVER_BEARER_TOKEN');");
        BundleAllocationAdmissionJob::unregister();
    }

}