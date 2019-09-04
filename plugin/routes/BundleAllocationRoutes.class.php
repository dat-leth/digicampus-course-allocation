<?php

class BundleAllocationRoutes extends \RESTAPI\RouteMap
{
    public function before()
    {

    }

    /**
     * GET: returns JSON with student preferences, list of bundle items, associated courses and exclusion map
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

        $exclStmt = $db->prepare("SELECT ex.*
        FROM bps_bundleitem_excluding ex
        JOIN bps_bundleitem bb on ex.item_id = bb.item_id
        JOIN bps_rankinggroup br on bb.group_id = br.group_id
        JOIN bpsadmissions b on br.rule_id = b.rule_id
        WHERE b.rule_id = ?;");
        $exclStmt->execute(array($ruleId));
        $exclResult = $exclStmt->fetchAll(PDO::FETCH_ASSOC);
        $overlaps = [];
        foreach ($exclResult as $excl) {
            $overlaps[$excl['item_id']]['bundle_item']['bundle_item_id'] = $excl['item_id'];
            $overlaps[$excl['item_id']]['overlapping_items'][]['bundle_item_id'] = $excl['excl_item_id'];
        }
        $json['overlaps'] = array_values($overlaps);

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
        return $json;
    }

    /**
     * POST: callback hook, receive JSON with allocations (student, course and priority of course)
     *
     * @post /bundleallocation/courseset/:setId/allocations
     */
    public function postAllocations($setId)
    {
        $db = DBManager::get();
        $stmt = $db->prepare("INSERT INTO `studip`.`bps_prelim_alloc` (user_id, item_id, seminar_id, priority) VALUES (?, ?, ?, ?);");
        foreach ($this->data as $alloc) {
            $stmt->execute(array($alloc['student'], $alloc['bundle_item'], $alloc['course'], $alloc['priority']));
            if ($stmt <= 0) {
                $this->error(400, 'Could not save allocations');
            }
        }

        return ['status' => '200', 'message' => 'Successfully saved allocations.'];
    }

    public function after()
    {

    }
}