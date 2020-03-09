<?php

require_once __DIR__ . '/../classes/RankingGroup.class.php';
require_once __DIR__ . '/../classes/BundleItem.class.php';

use BundleAllocation\RankingGroup;
use BundleAllocation\BundleItem;

class BundleAllocationAdmission extends \AdmissionRule
{
    // --- ATTRIBUTES ---
    public $applicationTime = 0;
    public $distributionTime = 0;
    public $jobId = '';
    public $distributionDone = false;
    public $minTimespanToDistrTime = 120;
    public $rankingGroups = [];
    public $courseCapacity;

    // --- OPERATIONS ---

    /**
     * Standard constructor.
     *
     * @param string $ruleId
     * @param string $courseSetId
     */
    public function __construct($ruleId = '', $courseSetId = '')
    {
        parent::__construct($ruleId, $courseSetId);
        $this->default_message = _('Unter Berücksichtigung der abgegebenen Prioritäten werden ein oder mehrere Veranstaltungen überschneidungsfrei zugeteilt.' .
            'Falls innerhalb des Anmeldezeitraums wiederholt die Eingabemaske zur ' .
            'Prioritätenerhebung nicht angezeigt wird, kontaktieren Sie die Lehrperson oder den Support.');

        if ($ruleId) {
            $this->load();
        } else {
            $this->id = $this->generateId('bpsadmissions');
            $this->rankingGroups = [];
        }
    }

    /**
     * Deletes the admission rule and all associated data.
     */
    public function delete()
    {
        parent::delete();
        // Delete rule data.
        $stmt = DBManager::get()->prepare("DELETE FROM `bpsadmissions` 
            WHERE `rule_id`=?");
        $stmt->execute(array($this->id));
    }

    /**
     * Gets some text that describes what this AdmissionRule (or respective
     * subclass) does.
     */
    public static function getDescription()
    {
        return _("Unter Berücksichtigung der abgegebenen Prioritäten werden dem Nutzer ein oder mehrere Veranstaltungen " .
            "überschneidungsfrei zugeteilt. Veranstaltungen haben eine maximale Teilnehmeranzahl.");
    }

    /**
     * Return this rule's name.
     */
    public static function getName()
    {
        return _("Präferenzbasierte überschneidungsfreie Anmeldung");
    }

    /**
     * Gets the time for seat distribution algorithm.
     *
     * @return int
     */
    public function getDistributionTime()
    {
        return $this->distributionTime;
    }

    /**
     * Set distribution time
     */
    public function setDistributionTime($time)
    {
        $this->distributionTime = $time;
        return $this;
    }

    public function getDistributionDone()
    {
        return $this->distributionDone;
    }

    public function setDistributionDone($done)
    {
        $this->distributionDone = $done;
        return $this;
    }

    public function getRankingGroups()
    {
        return $this->rankingGroups;
    }

    public function addRankingGroup($group)
    {
        $this->rankingGroups[$group->getId()] = $group;
        return $this;
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public function setJobId($id)
    {
        $this->jobId = $id;
        return $this;
    }

    public function getApplicationTime()
    {
        return $this->applicationTime;
    }

    public function setApplicationTime($time)
    {
        $this->applicationTime = $time;
        return $this;
    }


    /**
     * Gets the template that provides a configuration GUI for this rule.
     *
     * @return String
     */
    public function getTemplate()
    {
        $factory = new Flexi_TemplateFactory(dirname(__FILE__) . '/../views/configure_rule/');

        // Now open specific template for this rule and insert base template. 
        $tpl = $factory->open('configure');

        $data = [];
        $data['lang'] = getUserLanguage($GLOBALS['user']->id);
        $data['rule_id'] = $this->getId();
        $data['appl_date'] = $this->getApplicationTime() ? date('d.m.Y', $this->getApplicationTime()) : '';
        $data['appl_time'] = $this->getApplicationTime() ? date('H:i', $this->getApplicationTime()) : '';
        $data['dist_date'] = $this->getDistributionTime() ? date('d.m.Y', $this->getDistributionTime()) : '';
        $data['dist_time'] = $this->getDistributionTime() ? date('H:i', $this->getDistributionTime()) : '';
        $data['groups'] = new ArrayObject();
        foreach ($this->getRankingGroups() as $id => $group) {
            $data['groups'][$id] = ObjectBuilder::export($group);
        }
        $data['course_capacity'] = $this->courseCapacity;

        $atleast_one_ranking_exists = !empty(DBManager::get()->fetchOne("SELECT bbr.* FROM bps_bundleitem_ranking bbr
            JOIN bps_bundleitem_course bbc on bbr.item_id = bbc.item_id
            JOIN bps_rankinggroup br on bbr.group_id = br.group_id
            JOIN bpsadmissions b on br.rule_id = b.rule_id
            JOIN courseset_rule cr on b.rule_id = cr.rule_id
            WHERE cr.set_id = ?", [$this->courseSetId]));
        $data['locked'] = $atleast_one_ranking_exists;


        $tpl->set_attribute('data', $data);
        return $tpl->render();
    }

    /**
     * Internal helper function for loading rule definition from database.
     */
    public function load()
    {
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT * FROM `bpsadmissions`
            WHERE `rule_id`=? LIMIT 1");
        $stmt->execute(array($this->id));
        if ($current = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->applicationTime = $current['application_time'];
            $this->distributionTime = $current['distribution_time'];
            $this->jobId = $current['job_id'];
            $this->distributionDone = $current['distribution_done'];

            $rankingGroupsStmt = $db->prepare("SELECT `group_id` FROM `bps_rankinggroup` WHERE `rule_id` = ?");
            $rankingGroupsStmt->execute([$this->id]);
            while ($groupsResult = $rankingGroupsStmt->fetch(PDO::FETCH_ASSOC)) {
                $rankingGroup = new \BundleAllocation\RankingGroup($groupsResult['group_id']);
                $this->rankingGroups[$rankingGroup->getId()] = $rankingGroup;
            }
        }
    }

    /**
     * Does the current rule allow the given user to register as participant
     * in the given course? Never happens here as admission is completely
     * locked.
     *
     * @param String userId
     * @param String courseId
     * @return Array Any errors that occurred on admission.
     */
    public function ruleApplies($userId, $courseId)
    {
        // YOU CANNOT PASS!
        return array($this->getMessage());
    }

    /**
     * Helper function for storing data to DB.
     */
    public function store()
    {
        $db = DBManager::get();
        $stmt = $db->prepare("INSERT INTO `bpsadmissions`
            (`rule_id`, `application_time`, `distribution_time`, `distribution_done`, `mkdate`, `chdate`)
            VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
            `application_time`=VALUES(`application_time`),
            `distribution_time`=VALUES(`distribution_time`), 
            `distribution_done`=VALUES(`distribution_done`), 
            `chdate`=VALUES(`chdate`);");
        $stmt->execute(array($this->id, $this->applicationTime, $this->distributionTime, $this->distributionDone, time(), time()));

        $group_ids = implode("', '", array_keys($this->rankingGroups));
        $cleanupStmt = $db->prepare("DELETE FROM `bps_rankinggroup` WHERE `rule_id` = ? AND `group_id` NOT IN ('{$group_ids}');");
        $cleanupStmt->execute([$this->id]);

        foreach ($this->rankingGroups as $group) {
            $group->store();
        }

        foreach ($this->courseCapacity as $id => $capacity) {
            $seminar = Seminar::GetInstance($id);
            $seminar->admission_turnout = $capacity;
            $seminar->store();
        }

        return $this;
    }

    /**
     * A textual description of the current rule.
     *
     * @return String
     */
    public function toString()
    {
        $factory = new Flexi_TemplateFactory(dirname(__FILE__) . '/templates/');

        $tpl = $factory->open('info');
        $tpl->set_attribute('rule', $this);
        $tpl->set_attribute('coursesetId', $this->courseSetId);
        return $tpl->render();
    }

    /**
     * @param array $data
     * @return AdmissionRule
     */
    public function setAllData($data)
    {
        parent::setAllData($data);
        if (!$data['distributiontime']) {
            $data['distributiontime'] = '23:59';
        }
        $ddate = strtotime($data['distributiondate'] . ' ' . $data['distributiontime']);
        $this->setDistributionTime($ddate);

        if (!$data['applicationtime']) {
            $data['applicationtime'] = '23:59';
        }
        $adate = strtotime($data['applicationdate'] . ' ' . $data['applicationtime']);
        $this->setApplicationTime($adate);

        $this->courseCapacity = $data['course_capacity'];

        $this->rankingGroups = [];
        if (isset($data['groups'])) {
            foreach ($data['groups'] as $serialized_group) {
                $group = ObjectBuilder::build($serialized_group, 'BundleAllocation\RankingGroup');
                // Silently drop bundle items with courses not in courseset or empty bundle items
                foreach ($group->getBundleItems() as $item) {
                    if (array_diff(array_keys($item->getCourses()), array_keys($data['course_capacity'])) || empty($item->getCourses())) {
                        $group->removeBundleItemById($item->getId());
                    }
                }
                $this->addRankingGroup($group);
            }
        }
        return $this;
    }


    /**
     * @param $data
     * @return array
     */
    public function validate($data)
    {
        $errors = parent::validate($data);
        // Any courses in this courseset?
        if (!isset($data['course_capacity'])) {
            $errors[] = _('Dieses Anmeldeset enthält keine Veranstaltungen.');
        }

        if (!$data['applicationdate']) {
            $errors[] = _('Bitte geben Sie ein Datum an, ab dem die Anmeldung möglich ist.');
        }
        if (!$data['applicationtime']) {
            $data['applicationtime'] = '23:59';
        }
        $adate = strtotime($data['applicationdate'] . ' ' . $data['applicationtime']);

        // Validate distribution time, has to be atleast in minTimespan minutes
        if (!$data['distributiontime']) {
            $data['distributiontime'] = '23:59';
        }
        $ddate = strtotime($data['distributiondate'] . ' ' . $data['distributiontime']);

        if ($adate > $ddate) {
            $errors[] = sprintf(_('Der Start der Anmeldephase kann nicht später als das Ende sein.'));
        }

        if (!$data['distributiondate'] || $ddate < (time() + $this->minTimespanToDistrTime * 60)) {
            $errors[] = sprintf(_('Bitte geben Sie für die Platzverteilung ein Datum an, das weiter in der Zukunft liegt. Das frühestmögliche Datum ist %s.'), strftime('%x %R', time() + $this->minTimespanToDistrTime * 60));
        }

        // Atleast one ranking group required
        if (!isset($data['groups'])) {
            $errors[] = _('Bitte legen Sie mindestens eine Zuteilungsgruppe an.');
        } else {
            $rankingGroups = [];
            $assignedCourses = [];
            foreach ($data['groups'] as $serialized_group) {
                $group = ObjectBuilder::build($serialized_group, 'BundleAllocation\RankingGroup');
                $rankingGroups[$group->getId()] = $group;

                foreach ($group->getBundleItems() as $item) {
                    $assignedCourses = array_merge($assignedCourses, array_keys($item->getCourses()));
                }

                // Cannot be empty ranking group.
                if (empty(array_keys($group->getBundleItems()))) {
                    $errors[] = sprintf(_('"%s" wurde noch keine Veranstaltungen zugeordnet.'), $group->getName());
                }
            }

            // All courses in courseset have to be assigned to a ranking group.
            if ($data['course_capacity'] !== null) {
                if (!empty(array_diff(array_keys($data['course_capacity']), $assignedCourses))) {
                    $errors[] = _('Es gibt noch Veranstaltungen, die keiner Zuteilungsgruppe zugeordnet sind.');
                }
            }
        }

        return $errors;
    }
} /* end of class BundleAllocationAdmission */

