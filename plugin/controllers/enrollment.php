<?php
require_once 'app/models/calendar/schedule.php';

class EnrollmentController extends PluginController
{
    /**
     * @param $action
     * @param $args
     * @return bool|void
     * @throws Trails_Exception
     * @throws Trails_SessionRequiredException
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $this->course_id = $args[0];
        $this->course = Seminar::GetInstance($this->course_id);
    }

    public function apply_action()
    {
        $courseset = CourseSet::getSetForCourse($this->course_id);
        $rule = $courseset->getAdmissionRule('BundleAllocationAdmission');
        $this->rule_id = $rule->getId();
        $this->distribution_time = $rule->getDistributionTime();

        $db = DBManager::get();
        $ranking_group_stmt = $db->prepare("SELECT br.* FROM bps_rankinggroup br
            JOIN bps_bundleitem bb on br.group_id = bb.group_id
            JOIN bps_bundleitem_course bbc on bb.item_id = bbc.item_id
            WHERE bbc.seminar_id = ?;");
        $ranking_group_stmt->execute(array($this->course_id));
        $this->ranking_group = $ranking_group_stmt->fetchOne();

        PageLayout::setTitle($this->ranking_group['group_name'] . ' - Prioritätenabgabe');

        // rankable courses
        $courses_stmt = $db->prepare("SELECT bbc.* FROM bps_bundleitem_course bbc
            JOIN bps_bundleitem bb on bbc.item_id = bb.item_id
            JOIN bps_rankinggroup br on bb.group_id = br.group_id
            WHERE br.group_id = ?;");
        $courses_stmt->execute(array($this->ranking_group['group_id']));
        $this->courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($this->courses as $i => $course) {
            $sem = Seminar::GetInstance($course['seminar_id']);
            $this->courses[$i]['name'] = $sem->getName();
            $turnus_data = $sem->getUndecoratedData();
            if (!empty($turnus_data['regular']['turnus_data'])) {
                $keys = ['start_hour', 'start_minute', 'end_hour', 'end_minute', 'weekday'];
                foreach ($sem->getUndecoratedData()['regular']['turnus_data'] as $datum) {
                    $datum['weekday'] = ($datum['weekday'] + 6) % 7;
                    $this->courses[$i]['turnus'][] = array_intersect_key($datum, array_flip($keys));
//                    $this->courses[$i]['turnus'][] = $datum;
                }
            }
            $this->courses[$i]['formatted_date'] = $sem->getDatesHTML();
        }
        $names = array_column($this->courses, 'name');
        array_multisort($names, SORT_ASC, $this->courses);

        // saved ranking
        $ranking_stmt = $db->prepare("SELECT bbr.item_id from bps_bundleitem_ranking bbr
            WHERE bbr.group_id = ? AND bbr.user_id = ?
            ORDER BY bbr.priority;");
        $ranking_stmt->execute(array($this->ranking_group['group_id'], $GLOBALS['user']->id));
        $this->ranking = $ranking_stmt->fetchAll(PDO::FETCH_COLUMN);

        // show schedule if all rankables have regular date
        // existing schedule entries
        $schedule_settings = CalendarScheduleModel::getScheduleSettings();
        $show_hidden = false;
        $semester = Semester::findCurrent();
        $days = $schedule_settings['glb_days'];
        foreach ($days as $key => $day_number) {
            $days[$key] = ($day_number + 6) % 7;
        }
        $this->existing_entries = CalendarScheduleModel::getEntries(
            $GLOBALS['user']->id,
            $semester,
            $schedule_settings['glb_start_time'],
            $schedule_settings['glb_end_time'],
            $days,
            $show_hidden
        );
        foreach ($this->existing_entries as $column) {
            $column->setURL(false);
            foreach ($column->entries as $key => $entry) {
                unset($column->entries[$key]['url']);
                unset($column->entries[$key]['onClick']);
                unset($column->entries[$key]['icons']);
                $column->entries[$key]['day'] = ($entry['day'] + 6) % 7;
            }
        }

        // existing rankings of same rule
        $other_rankings_stmt = $db->prepare("SELECT bbr.group_id, br.group_name, bbc.item_id, bbc.seminar_id, bbr.priority FROM bps_bundleitem_ranking bbr
            JOIN bps_bundleitem_course bbc on bbr.item_id = bbc.item_id
            JOIN bps_rankinggroup br on bbr.group_id = br.group_id
            JOIN bpsadmissions b on br.rule_id = b.rule_id
            WHERE bbr.user_id = ? AND br.group_id != ? AND b.rule_id = ?;");
        $other_rankings_stmt->execute(array($GLOBALS['user']->id, $this->ranking_group['group_id'], $this->rule_id));
        $this->other_rankings = $other_rankings_stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($this->other_rankings)) {
            foreach ($this->other_rankings as $i => $other) {
                $sem = Seminar::GetInstance($other['seminar_id']);
                $this->other_rankings[$i]['name'] = $sem->getName();
                $turnus_data = $sem->getUndecoratedData();
                if (!empty($turnus_data['regular']['turnus_data'])) {
                    $keys = ['start_hour', 'start_minute', 'end_hour', 'end_minute', 'weekday'];
                    foreach ($sem->getUndecoratedData()['regular']['turnus_data'] as $datum) {
                        $datum['weekday'] = ($datum['weekday'] + 6) % 7;
                        $this->other_rankings[$i]['turnus'][] = array_intersect_key($datum, array_flip($keys));
                    }
                }
                $this->other_rankings[$i]['formatted_date'] = $sem->getDatesHTML();
            }
        }

        $other_ranking_groups_stmt = $db->prepare("SELECT group_name FROM bps_rankinggroup
WHERE rule_id = ?;");
        $other_ranking_groups_stmt->execute(array($this->rule_id));
        $this->other_ranking_groups = $other_ranking_groups_stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function submit_preferences_action()
    {
        if (Request::isPost()) {
            $request = trim(file_get_contents("php://input"));
            $decoded_request = json_decode($request, true);
            if ($decoded_request === NULL) {
                throw new Trails_Exception(500, 'bad request, could not decode json');
            }

            $db = DBManager::get();
            $stmt = $db->prepare("INSERT INTO `studip`.`bps_bundleitem_ranking` (`user_id`, `group_id`, `item_id`, `priority`) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE priority=VALUES(priority), chdate=UNIX_TIMESTAMP()");
            foreach ($decoded_request['ranking'] as $i => $item_id) {
                $stmt->execute(array($GLOBALS['user']->id, $decoded_request['group_id'], $item_id, $i));
            }
            $item_ids = $decoded_request['ranking'];
            if (count($item_ids) > 0) {
                $in = str_repeat('?,', count($item_ids) - 1) . '?';
                $delete_stmt = $db->prepare("DELETE FROM `studip`.`bps_bundleitem_ranking` WHERE user_id = ? AND group_id = ? AND item_id NOT IN ($in);");
                $delete_stmt->execute(array_merge([$GLOBALS['user']->id, $decoded_request['group_id']], $item_ids));
            } else {
                $db->prepare("DELETE FROM `studip`.`bps_bundleitem_ranking` WHERE user_id = ? AND group_id = ?")
                    ->execute([$GLOBALS['user']->id, $decoded_request['group_id']]);
            }

            if ($stmt > 0) {
                $this->set_content_type('application/json');
                $this->render_text(json_encode(array('status' => 'success')));
            } else {
                $this->set_status(400);
                $this->set_content_type('application/json');
                $this->render_text(json_encode(array('status' => 'failed')));
            }
        } else {
            $this->set_status(405);
            $this->set_content_type('application/json');
            $this->render_text(json_encode(array('status' => 'not allowed')));
        }
    }
}