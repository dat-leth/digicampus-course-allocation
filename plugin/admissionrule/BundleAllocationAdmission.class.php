<?php

/**
 * BundleAllocationAdmission.class.php
 *
 * Represents a rule for completely locking courses for admission.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class BundleAllocationAdmission extends AdmissionRule
{
    // --- ATTRIBUTES ---
    public $distribution_time = 0;
    public $job_id = null;
    public $distribution_done = false;

    // --- OPERATIONS ---

    /**
     * Standard constructor.
     *
     * @param String ruleId
     */
    public function __construct($ruleId = '', $courseSetId = '')
    {
        parent::__construct($ruleId, $courseSetId);
        $this->default_message = _('Unter Berücksichtigung der abgegebenen Prioritäten werden ein oder mehrere Veranstaltungen überschneidungsfrei zugeteilt.' .
            'Falls innerhalb des Anmeldezeitraums wiederholt die Eingabemaske zur ' .
            'Prioritätenerhebung nicht angezeigt wird, kontaktieren Sie die Lehrperson oder den Administrator.');

        if ($ruleId) {
            $this->load();
        } else {
            $this->id = $this->generateId('bpsadmissions');
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
     * Gets the template that provides a configuration GUI for this rule.
     *
     * @return String
     */
    public function getTemplate()
    {
        $factory = new Flexi_TemplateFactory(dirname(__FILE__) . '/../views/configurerule/');
        // Now open specific template for this rule and insert base template. 
        $tpl = $factory->open('configure');
        $tpl->set_attribute('rule', $this);
        $tpl->set_attribute('courseset_id', $this->courseSetId);
        $courses = CourseSet::getCoursesByCourseSetId($this->courseSetId);
        $tpl->set_attribute('courses', $courses);
        return $tpl->render();
    }

    /**
     * Internal helper function for loading rule definition from database.
     */
    public function load()
    {
        $stmt = DBManager::get()->prepare("SELECT * FROM `bpsadmissions`
            WHERE `rule_id`=? LIMIT 1");
        $stmt->execute(array($this->id));
        if ($current = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->message = $current['message'];
            $this->distribution_time = $current['distribution_time'];
            $this->job_id = $current['job_id'];
            $this->distribution_done = $current['distribution_done'];
        }
        if (empty($this->courseSetId)) {
            $result = DBManager::get()->fetchOne("SELECT `set_id` FROM `courseset_rule` WHERE `rule_id`=?",
                array($this->getId()));
            $this->courseSetId = $result['set_id'];
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
        // Store data.
        $stmt = DBManager::get()->prepare("INSERT INTO `bpsadmissions`
            (`rule_id`, `message`, `distribution_time`, `mkdate`, `chdate`)
            VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
            `message`=VALUES(`message`), `distribution_time`=VALUES(`distribution_time`), `chdate`=VALUES(`chdate`)");
        $stmt->execute(array($this->id, $this->default_message, $this->distribution_time, time(), time()));
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
        return $tpl->render();
    }

    /**
     * @param $data
     * @return array
     */
    public function validate($data)
    {
        $errors = parent::validate($data);
        if ($data['preventSave'] == 'true') {
            $errors[] = _('Bitte überprüfen Sie Ihre Eingaben.');
        }
        return $errors;
    }

} /* end of class BundleAllocationAdmission */

