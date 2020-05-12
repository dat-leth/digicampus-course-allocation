<?php

require_once __DIR__ . '/../composer_modules/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use function GuzzleHttp\Promise\all;

$config = include(__DIR__ . '/../config.inc.php');

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
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT cr.set_id, cr.rule_id, bpsadmissions.distribution_time, bpsadmissions.distribution_done, bpsadmissions.job_id FROM bpsadmissions
            JOIN courseset_rule cr on bpsadmissions.rule_id = cr.rule_id
            WHERE bpsadmissions.distribution_done = 0;"
        );
        $stmt->execute();
        $sets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $client = new GuzzleHttp\Client([
            'base_uri' => $config->microservice_url,
            RequestOptions::ALLOW_REDIRECTS => false
        ]);

        $new_alloc_requests = [];
        $get_status_requests = [];
        foreach ($sets as $set) {
            if (empty($set['job_id'])) {
                $new_alloc_requests[$set['set_id']] = $client->requestAsync('POST', 'allocation', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $config->bearer_token,
                    ],
                    'body' => json_encode([
                        'callbackUrl' => URLHelper::getURL('api.php/bundleallocation/courseset/' . $set['set_id'] . '/allocations'),
                        'studentPreferences' => URLHelper::getURL('api.php/bundleallocation/courseset/' . $set['set_id'] . '/preferences')
                    ], JSON_UNESCAPED_SLASHES)
                ]);
            } else if (!empty($set['job_id'])) {
                $get_status_requests[$set['set_id']] = $client->requestAsync('GET', 'job/' . $set['job_id'],
                    ['headers' => ['Authorization' => 'Bearer ' . $config->bearer_token]]);
            }
        }
        $promises = GuzzleHttp\Promise\settle($new_alloc_requests)->wait();
        foreach ($promises as $setId => $promise) {
            if ($promise['state'] == 'fulfilled') {
                $body = json_decode($promise['value']->getBody());
                $db->execute("UPDATE bpsadmissions bps JOIN courseset_rule cr on bps.rule_id = cr.rule_id SET bps.job_id = ?, bps.chdate = UNIX_TIMESTAMP() WHERE cr.set_id = ?;",
                    [$body->job_id, $setId]);
                echo date('r') . ' ' . $setId . ' Allocation submitted to server. Received Job ID ' . $body->job_id, PHP_EOL;
            } else {
                echo date('r') . ' ' . $setId . ' Could not submit allocation request to server for ' . $setId, PHP_EOL;
                echo $promise['reason']->getMessage(), PHP_EOL;
            }
        }

        $promises = GuzzleHttp\Promise\settle($get_status_requests)->wait();
        foreach ($promises as $setId => $promise) {
            if ($promise['state'] == 'fulfilled' && $promise['value']->getStatusCode() == 200) {
                echo date('r') . ' ' . $setId . ' Allocation process has ' . $promise['value']->getBody(), PHP_EOL;
            } else if ($promise['state'] == 'fulfilled' && $promise['value']->getStatusCode() == 303) {
                echo date('r') . ' ' . $setId . ' Allocation process is done. Fetching results from server.', PHP_EOL;
                $this->fetchResults($setId, $promise['value']->getHeader('Location')[0]);
            } else {
                echo date('r') . ' ' . $setId . ' Fetching job status failed.', PHP_EOL;
            }
        }

        $stmt->execute();
        $sets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sets as $set) {
            if ($set['chdate'] < (time() - 21600) && $set['distribution_done'] == false) {
                $courseset = new CourseSet($set['set_id']);
                $rule = $courseset->getAdmissionRule('BundleAllocationAdmission');
                $rule->setDistributionDone(true);
                $rule->store();
                setTempLanguage($courseset->getUserId());
                $msg_title = sprintf(_('Anmeldeset %s: Verteilung möglicherweise fehlgeschlagen'), $courseset->getName());
                $msg_body = sprintf(_('Das Anmeldeset %s mit Verteilzeitpunkt %s hat bisher noch keine Ergebnisse erhalten. Möglicherweise ist die Verteilung fehlgeschlagen. Überprüfen Sie, ob der Cronjob bzw. der externe Service ausgeführt wird, Netzwerkfehler vorliegen oder fehlerhafte Daten übermittelt werden. Fordern Sie anschließend eine neue Verteilung unter "Anmeldungen verwalten" %s an.'),
                    $courseset->getName(), date("d.m.Y h:i", $rule->getDistributionTime()), PluginEngine::getLink('BundleAllocationPlugin', [], 'admission/applications/' . $set['set_id']));
                messaging::sendSystemMessage($courseset->getUserId(), $msg_title, $msg_body);
                restoreLanguage();
                echo date('r') . ' ' . $set['set_id'] . ' Timeout - no results after 6 hours. Courseset owner has been notified. Polling for results disabled.', PHP_EOL;
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
        $stmt = $db->prepare("INSERT INTO `bps_prelim_alloc` (user_id, group_id, item_id, seminar_id, priority, waitlist) 
                VALUES (?, ?, ?, ?, ?, FALSE) 
                ON DUPLICATE KEY UPDATE item_id=VALUES(item_id), seminar_id=VALUES(seminar_id), priority=VALUES(priority);");
        foreach ($data as $alloc) {
            $stmt->execute(array($alloc->student, $alloc->ranking_group, $alloc->bundle_item, $alloc->course, $alloc->priority));
        }

        setTempLanguage($set->getUserId());
        $msg_title = sprintf(_('Anmeldeset %s: Verteilergebnisse stehen zur Verfügung'), $set->getName());
        $msg_body = sprintf(_('Für das Anmeldeset %s (%s) mit präferenzbasierter überschneidungsfreier Anmeldung wurde eine Verteilung berechnet. Die Ergebnisse können unter %s eingesehen werden und ggf. angepasst werden. Die Teilnehmer sind **noch nicht** in den Veranstaltungen eingetragen. Die Zuteilungen müssen vorher bestätigt werden.'),
            $set->getName(), date("d.m.Y h:i", $rule->getDistributionTime()), PluginEngine::getLink('BundleAllocationPlugin', [], 'admission/applications/' . $setId));
        messaging::sendSystemMessage($set->getUserId(), $msg_title, $msg_body);
        restoreLanguage();

        $rule->setDistributionDone(true);
        $rule->store();
    }
}