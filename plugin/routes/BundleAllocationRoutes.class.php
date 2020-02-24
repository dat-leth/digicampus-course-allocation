<?php

class BundleAllocationRoutes extends \RESTAPI\RouteMap
{
    public function before()
    {

    }

    /**
     * Returns JSON with student preferences, list of bundle items, associated courses and exclusion map
     *
     * @get /bundleallocation/courseset/:setId/preferences
     */
    public function getPreferences($setId)
    {
        $json = ['bundle_items' => [], 'courses' => [], 'overlaps' => [], 'students' => []];
        $db = DBManager::get();

        $courseset = new CourseSet($setId);
        $rule = $courseset->getAdmissionRule('BundleAllocationAdmission');
        if (empty($rule)) {
            $this->error(400, 'Courseset has no bundle allocation rule');
        }
        $ruleId = $rule->getId();


        $courseIds = $courseset->getCourses();
        foreach ($courseIds as $courseId) {
            $course = Course::find($courseId);
            $course_json = [];
            $course_json['course_id'] = $courseId;
            $course_json['course_name'] = $course->getFullname('number-name-semester');
            $course_json['capacity'] = (int)$course->admission_turnout;
            $json['courses'][] = $course_json;
        }

        $bundleItemsStmt = $db->prepare("SELECT c.item_id, c.seminar_id, bb.group_id
         FROM bps_bundleitem_course c
         JOIN bps_bundleitem bb on c.item_id = bb.item_id
         JOIN bps_rankinggroup br on bb.group_id = br.group_id
         JOIN bpsadmissions b on br.rule_id = b.rule_id
         WHERE b.rule_id = ?;");
        $bundleItemsStmt->execute(array($ruleId));
        $bundleItemsResult = $bundleItemsStmt->fetchAll(PDO::FETCH_ASSOC);
        $bundleItems = [];
        foreach ($bundleItemsResult as $item) {
            $bundleItems[$item['item_id']]['bundle_item_id'] = $item['item_id'];
            $bundleItems[$item['item_id']]['ranking_group'] = $item['group_id'];
            $bundleItems[$item['item_id']]['sum_capacity'] += Course::find($item['seminar_id'])->admission_turnout;
            $bundleItems[$item['item_id']]['courses'][]['course_id'] = $item['seminar_id'];
        }
        $json['bundle_items'] = array_values($bundleItems);

        $rankingStmt = $db->prepare("SELECT r.*
        FROM bps_bundleitem_ranking r
        JOIN bps_rankinggroup br on r.group_id = br.group_id
        JOIN bpsadmissions b on br.rule_id = b.rule_id
        WHERE b.rule_id = ?
        ORDER BY r.user_id, r.priority;");
        $rankingStmt->execute(array($ruleId));
        $rankingResult = $rankingStmt->fetchAll(PDO::FETCH_ASSOC);
        $rankings = [];
        foreach ($rankingResult as $ranking) {
            $rankings[$ranking['user_id']]['student_id'] = $ranking['user_id'];
            $rankings[$ranking['user_id']]['rankings'][$ranking['group_id']][]['bundle_item_id'] = $ranking['item_id'];
        }
        $json['students'] = array_values($rankings);

        $overlapStmt = $db->prepare("
        SELECT DISTINCT bbc_base.item_id,
                cycle_base.seminar_id,
                cycle_base.start_time,
                cycle_base.end_time,
                cycle_base.weekday,
                bbc_comp.item_id as `excl_item_id`,
                cycle_comp.seminar_id,
                cycle_comp.start_time,
                cycle_comp.end_time,
                cycle_comp.weekday

        FROM seminar_cycle_dates AS cycle_base
                 INNER JOIN seminar_cycle_dates AS cycle_comp
                            ON
                                    cycle_base.start_time < cycle_comp.end_time
                                    AND
                                    cycle_base.end_time > cycle_comp.start_time
                 INNER JOIN bps_bundleitem_course bbc_base on cycle_base.seminar_id = bbc_base.seminar_id
                 INNER JOIN bps_bundleitem bb_base on bbc_base.item_id = bb_base.item_id
                 INNER JOIN bps_rankinggroup br_base on bb_base.group_id = br_base.group_id
                 INNER JOIN bpsadmissions b_base on br_base.rule_id = b_base.rule_id
        
                 INNER JOIN bps_bundleitem_course bbc_comp on cycle_comp.seminar_id = bbc_comp.seminar_id
                 INNER JOIN bps_bundleitem bb_comp on bbc_comp.item_id = bb_comp.item_id
                 INNER JOIN bps_rankinggroup br_comp on bb_comp.group_id = br_comp.group_id
                 INNER JOIN bpsadmissions b_comp on br_comp.rule_id = b_comp.rule_id
        WHERE b_base.rule_id = ?
          AND b_comp.rule_id = ?
        ORDER BY bbc_base.item_id;");
        $overlapStmt->execute(array($ruleId, $ruleId));
        $overlapResult = $overlapStmt->fetchAll(PDO::FETCH_ASSOC);
        $overlaps = [];
        foreach ($overlapResult as $overlap) {
            $overlaps[$overlap['item_id']]['bundle_item']['bundle_item_id'] = $overlap['item_id'];
            $overlaps[$overlap['item_id']]['overlapping_items'][]['bundle_item_id'] = $overlap['excl_item_id'];
        }
        $json['overlaps'] = array_values($overlaps);

        return $json;
    }

    /**
     * Callback hook, receive JSON with allocations (student to course and assigned priorities)
     *
     * @post /bundleallocation/courseset/:setId/allocations
     */
    public function postAllocations($setId)
    {
        $set = new CourseSet($setId);
        $rule = $set->getAdmissionRule('BundleAllocationAdmission');

        if (!empty($rule) && !$rule->getDistributionDone()) {
            $db = DBManager::get();
            $stmt = $db->prepare("INSERT INTO `bps_prelim_alloc` (user_id, group_id, item_id, seminar_id, priority, waitlist) 
                VALUES (?, ?, ?, ?, ?, FALSE) 
                ON DUPLICATE KEY UPDATE item_id=VALUES(item_id), seminar_id=VALUES(seminar_id), priority=VALUES(priority);");

            foreach ($this->data as $alloc) {
                $stmt->execute(array($alloc['student'], $alloc['ranking_group'], $alloc['bundle_item'], $alloc['course'], $alloc['priority']));
                if ($stmt <= 0) {
                    $this->error(400, 'Could not save allocations');
                }
            }
            $rule->setDistributionDone(true);
            $rule->store();
            $msg_title = sprintf('Anmeldeset %s: Verteilergebnisse stehen zur Verfügung', $set->getName());
            $msg_body = sprintf('Für das Anmeldeset %s (%s) mit präferenzbasierter überschneidungsfreier Anmeldung wurde eine Verteilung berechnet. Die Ergebnisse können unter %s eingesehen werden und ggf. angepasst werden. Die Teilnehmer sind **noch nicht** in den Veranstaltungen eingetragen. Die Zuteilungen müssen vorher bestätigt werden.',
                $set->getName(), date("d.m.Y h:i", $rule->getDistributionTime()), PluginEngine::getURL('BundleAllocationPlugin', [], 'admission/applications/' . $setId));
            messaging::sendSystemMessage($set->getUserId(), $msg_title, $msg_body);

            return ['status' => '200', 'message' => 'Successfully saved allocations.', 'done' => $rule->getDistributionDone()];
        }

        $this->error(400, 'Could not save allocations');
        return [];
    }


    public function after()
    {

    }
}