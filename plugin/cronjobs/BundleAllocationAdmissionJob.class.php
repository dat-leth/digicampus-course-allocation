<?php

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use function GuzzleHttp\Promise\all;

require_once __DIR__ . '/../composer_modules/autoload.php';

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
     * TODO: Add secure OAuth connection to server
     *
     * @param mixed $last_result What the last execution of this cronjob
     *                           returned.
     * @param Array $parameters Parameters for this cronjob instance which
     *                          were defined during scheduling.
     */
    public function execute($last_result, $parameters = array())
    {
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT cr.set_id, cr.rule_id, bpsadmissions.distribution_time, bpsadmissions.distribution_done, bpsadmissions.job_id FROM bpsadmissions
            JOIN courseset_rule cr on bpsadmissions.rule_id = cr.rule_id
            WHERE bpsadmissions.distribution_done = 0;"
        );
        $stmt->execute();
        $sets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $base_endpoint_url = Config::get()->BUNDLEALLOCATION_SERVER_ENDPOINT;
        $client = new GuzzleHttp\Client([
            'base_uri' => $base_endpoint_url,
            RequestOptions::ALLOW_REDIRECTS => false
        ]);

        $new_alloc_requests = [];
        $get_status_requests = [];
        foreach ($sets as $set) {
            if (empty($set['job_id'])) {
                $new_alloc_requests[$set['set_id']] = $client->requestAsync('POST', 'allocation', [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'callbackUrl' => URLHelper::getURL('api.php/bundleallocation/courseset/' . $set['set_id'] . '/allocations'),
                        'studentPreferences' => URLHelper::getURL('api.php/bundleallocation/courseset/' . $set['set_id'] . '/preferences')
                    ], JSON_UNESCAPED_SLASHES)
                ]);
            } else if (!empty($set['job_id'])) {
                $get_status_requests[$set['set_id']] = $client->requestAsync('GET', 'job/' . $set['job_id']);
            }
        }
        $promises = GuzzleHttp\Promise\settle($new_alloc_requests)->wait();
        foreach ($promises as $setId => $promise) {
            if ($promise['state'] == 'fulfilled') {
                $body = json_decode($promise['value']->getBody());
                $db->execute("UPDATE bpsadmissions bps JOIN courseset_rule cr on bps.rule_id = cr.rule_id SET bps.job_id = ?, bps.chdate = UNIX_TIMESTAMP() WHERE cr.set_id = ?;",
                    [$body->job_id, $setId]);
                echo date('r') . ' ' . $setId . ' Allocation submitted to server. Received Job ID ' . $body->job_id;
            } else {
                echo 'Could not submit allocation request to server for ' . $setId . '\n';
                print_r($promise);
            }
        }

        $promises = GuzzleHttp\Promise\settle($get_status_requests)->wait();
        foreach ($promises as $setId => $promise) {
            if ($promise['state'] == 'fulfilled' && $promise['value']->getStatusCode() == 200) {
                echo date('r') . ' ' . $setId . ' Allocation process has ' . $promise['value']->getBody();
            } else if ($promise['state'] == 'fulfilled' && $promise['value']->getStatusCode() == 303) {
                echo date('r') . ' ' . $setId . ' Allocation process is done. Fetching results from server.';
                $this->fetchResults($setId, $promise['value']->getHeader('Location')[0]);
            } else {
                echo date('r') . ' ' . $setId . ' Fetching job status failed.';
                print_r($promise);
            }
        }

        $stmt->execute();
        $sets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sets as $set) {
            if ($set['distribution_time'] < (time() - 21600) && ($set['distribution_done'] == false || empty($set['job_id']))) {
                $courseset = new CourseSet($set['set_id']);
                $rule = $set->getAdmissionRule('BundleAllocationAdmission');
                $msg_title = sprintf('Anmeldeset %s: Verteilung möglicherweise fehlgeschlagen', $courseset->getName());
                $msg_body = sprintf('Das Anmeldeset %s mit Verteilzeitpunkt %s hat bisher noch keine Ergebnisse erhalten. Möglicherweise ist die Verteilung fehlgeschlagen. Überprüfen Sie, ob der Cronjob bzw. der externe Service ausgeführt wird, Netzwerkfehler vorliegen oder fehlerhafte Daten übermittelt werden.',
                    $courseset->getName(), date("d.m.Y h:i", $rule->getDistributionTime()));
                messaging::sendSystemMessage($courseset->getUserId(), $msg_title, $msg_body);
            }
        }
    }

    private function fetchResults($setId, $locationUrl)
    {
        $set = new CourseSet($setId);
        $rule = $set->getAdmissionRule('BundleAllocationAdmission');

        $client = new GuzzleHttp\Client();
        $response = $client->request('GET', $locationUrl);
        $data = json_decode($response->getBody());
        $db = DBManager::get();
        $stmt = $db->prepare("INSERT INTO `studip`.`bps_prelim_alloc` (user_id, item_id, seminar_id, priority) VALUES (?, ?, ?, ?);");
        foreach ($data as $alloc) {
            $stmt->execute(array($alloc->student, $alloc->bundle_item, $alloc->course, $alloc->priority));
        }

        $msg_title = sprintf('Anmeldeset %s: Verteilergebnisse stehen zur Verfügung', $set->getName());
        $msg_body = sprintf('Für das Anmeldeset %s (%s) mit präferenzbasierter überschneidungsfreier Anmeldung wurde eine Verteilung berechnet. Die Ergebnisse können unter %s eingesehen werden und ggf. angepasst werden. Die Teilnehmer sind **noch nicht** in den Veranstaltungen eingetragen. Die (angepasste) Verteilung muss vorher bestätigt werden.',
            $set->getName(), date("d.m.Y h:i", $rule->getDistributionTime()), PluginEngine::getLink('BundleAllocationPlugin', [], 'admission/'.$setId));
        messaging::sendSystemMessage($set->getUserId(), $msg_title, $msg_body);
        $rule->setDistributionDone(true);
        $rule->store();
    }
}