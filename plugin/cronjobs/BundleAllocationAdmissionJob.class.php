<?php


class BundleAllocationAdmissionJob extends CronJob
{

    /**
     * Return the name of the cronjob.
     */
    public static function getName()
    {
        return _('Bundle Allocation Zuteilung ausführen und überprüfen');
    }

    /**
     * Return the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Überprüft, ob Zuteilung durch Bundle Allocation erfolgreich. Triggert Zuteilung für Anmeldeset beim Microservice, falls noch kein Zuteilungsjob. Ansonsten abfragen des Status bzw. Zuteilungsergebnisses');
    }

    /**
     * Execute the cronjob.
     *
     * @param mixed $last_result What the last execution of this cronjob
     *                           returned.
     * @param Array $parameters Parameters for this cronjob instance which
     *                          were defined during scheduling.
     */
    public function execute($last_result, $parameters = array())
    {
        // TODO: Implement execute() method.
        // Fetch courseset ids from database where BundleAllocationAdmissionRule and non completed allocation
        // Create courseset object
        // Check if allocation job exists
        // If not: submit allocation request to microservice
        // Else: query job and get status or result
    }
}