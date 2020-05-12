<?php
require_once __DIR__ . '/../cronjobs/BundleAllocationAdmissionJob.class.php';

class SetupCronjob extends Migration
{
    /**
     *
     */
    function up()
    {
        BundleAllocationAdmissionJob::register()->schedulePeriodic(-15)->activate();
    }

    /**
     *
     */
    function down()
    {
        BundleAllocationAdmissionJob::unregister();
    }

}