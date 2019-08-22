<?php


class ConfigController extends PluginController
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
        if (!($GLOBALS['perm']->have_perm('admin') || ($GLOBALS['perm']->have_perm('dozent') && get_config('ALLOW_DOZENT_COURSESET_ADMIN')))) {
            throw new AccessDeniedException();
        }
    }

    public function index_action()
    {
        PageLayout::setTitle('Bundle Allocation verwalten');
        PageLayout::setTabNavigation('/tools');
        Navigation::activateItem('/tools/coursesets/bps_config');
    }

    public function courses_capacity_action($courseset_id)
    {
        $courses = CourseSet::getCoursesByCourseSetId($courseset_id);

        if (Request::isGet()) {
            if (empty($courses)) {
                throw new Trails_Exception(404, 'courseset not found');
            }
            foreach ($courses as $i => $course) {
                $courses[$i] = ['seminar_id' => $course['seminar_id'],
                    'capacity' => (int)Course::find($course['seminar_id'])->admission_turnout];
            }
            $this->set_content_type('application/json');
            $this->render_text(json_encode($courses));
        } elseif (Request::isPost()) {
            $request = trim(file_get_contents("php://input"));
            $decoded_request = json_decode($request, true);
            if ($decoded_request === NULL) {
                throw new Trails_Exception(400, 'bad request, could not decode json');
            }
            $course_ids = array_column($decoded_request, 'seminar_id');
            $capacities = array_column($decoded_request, 'capacity');
            foreach ($capacities as $c) {
                if (filter_var($c, FILTER_VALIDATE_INT) === false) {
                    throw new Trails_Exception(400, 'bad request, validation error');
                }
            }
            $courses = Course::findMany($course_ids);
            if (count($decoded_request) != count($courses)) {
                throw new Trails_Exception(400, 'bad request, courses not found');
            }
            foreach ($decoded_request as $elem) {
                foreach ($courses as $course) {
                    if ($elem['seminar_id'] == $course->seminar_id) {
                        $course->admission_turnout = $elem['capacity'];
                        $course->store();
                    }
                }
            }
            $this->render_text(json_encode(array("status" => "success")));
        } else {
            throw new Trails_Exception(400, 'Bad Request');
        }
    }

    public function courses_cycles_action($courseset_id)
    {
        $courses = CourseSet::getCoursesByCourseSetId($courseset_id);
        if (empty($courses)) {
            throw new Trails_Exception(404, 'courseset not found');
        }
        foreach ($courses as $i => $course) {
            $courses[$i]['seminar_id'] = $course['seminar_id'];
            $cycles = SeminarCycleDate::findBySeminar($course['seminar_id']);
            foreach ($cycles as $j => $cycle) {
                $courses[$i]['cycles'][$j]['start_time'] = $cycle->start_time;
                $courses[$i]['cycles'][$j]['end_time'] = $cycle->end_time;
                $courses[$i]['cycles'][$j]['weekday'] = $cycle->weekday;
            }
        }
        $this->set_content_type('application/json');
        $this->render_text(json_encode($courses));
    }

    public function ranking_groups_action($rule_id)
    {
        $db = DBManager::get();
        if (Request::isPost()) {
            $request = trim(file_get_contents("php://input"));
            $decoded_request = json_decode($request, true);
            if ($decoded_request === NULL) {
                throw new Trails_Exception(500, 'bad request, could not decode json');
            }
            $delete_stmt = $db->prepare("DELETE FROM `bps_rankinggroup` WHERE `rule_id` = ?");
            $delete_stmt->execute(array($rule_id));
            foreach ($decoded_request as $rankinggroup) {
                $stmt = $db->prepare(
                    "INSERT INTO `studip`.`bps_rankinggroup` (`group_id`, `rule_id`, `group_name`, `min_amount_prios`) VALUES (?, ?, ?, ?)"
                );
                if (empty($rankinggroup['group_id'])) {
                    $group_id = uniqid('bps_rg_');
                } else {
                    $group_id = $rankinggroup['group_id'];
                }
                $stmt->execute(array($group_id, $rule_id, $rankinggroup['group_name'], $rankinggroup['min_amount_prios']));
            }
        }

        $stmt = $db->prepare("SELECT * FROM `bps_rankinggroup` WHERE rule_id=?");
        $stmt->execute(array($rule_id));
        if ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
            $this->set_content_type('application/json');
            $this->render_text(json_encode($result));
        } else {
            $this->set_content_type('application/json');
            $this->render_text(json_encode([]));
        }
    }

    public function bundleitems_action($group_id)
    {
        $db = DBManager::get();
        if (Request::isPost()) {
            $request = trim(file_get_contents("php://input"));
            $decoded_request = json_decode($request, true);
            if ($decoded_request === NULL) {
                throw new Trails_Exception(500, 'bad request, could not decode json');
            }
            $ranking_group_stmt = $db->prepare("SELECT * FROM studip.bps_rankinggroup WHERE group_id = ?");
            $ranking_group_stmt->execute(array($group_id));
            if (!$ranking_group_stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Trails_Exception(404, 'group not found');
            }
            $insert_item_stmt = $db
                ->prepare("INSERT INTO `studip`.`bps_bundleitem` (`item_id`, `group_id`, `start_time`, `end_time`, `weekday`) VALUES (?, ?, ?, ?, ?);");
            $delete_items_stmt = $db
                ->prepare("DELETE bps_bundleitem FROM bps_bundleitem WHERE bps_bundleitem.group_id = ?;");
            $course_stmt = $db
                ->prepare("INSERT INTO `studip`.`bps_bundleitem_course` (`item_id`, `seminar_id`) VALUES (?, ?);");

            $delete_items_stmt->execute(array($group_id));

            foreach ($decoded_request as $item) {
                if (empty($item['item_id'])) {
                    $item_id = uniqid('bps_bi_');
                } else {
                    $item_id = $item['item_id'];
                }

                $insert_item_stmt->execute(array($item_id, $group_id,
                    $item['start_time'], $item['end_time'], $item['weekday']));
                $db
                    ->prepare("DELETE FROM `studip`.`bps_bundleitem_course` WHERE `item_id` LIKE ? ESCAPE '#'")
                    ->execute(array($item_id));
                foreach ($item['seminar_ids'] as $id) {
                    $course_stmt->execute(array($item_id, $id));
                }

            }
        }

        $stmt = $db->prepare(
            "SELECT bi.* FROM bps_bundleitem bi
            WHERE bi.group_id = ?;"
        );
        $stmt->execute(array($group_id));
        if ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
            foreach ($result as $i => $item) {
                $stmt = $db
                    ->prepare("SELECT seminar_id from bps_bundleitem_course bbc
                        JOIN bps_bundleitem bi on bbc.item_id = bi.item_id
                        WHERE bi.item_id = ?;");
                $stmt->execute(array($item['item_id']));
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $result[$i]['seminar_ids'] = array_column($courses, 'seminar_id');
            }
            $this->set_content_type('application/json');
            $this->render_text(json_encode($result));
        } else {
            $this->set_content_type('application/json');
            $this->render_text(json_encode([]));
        }

    }

    public function bundleitem_excl_action($rule_id)
    {
        $db = DBManager::get();
        if (Request::isPost()) {
            $request = trim(file_get_contents("php://input"));
            $decoded_request = json_decode($request, true);
            if ($decoded_request === NULL) {
                throw new Trails_Exception(500, 'bad request, could not decode json');
            }
            $delete_stmt = $db->prepare(
                "DELETE ex FROM bps_bundleitem_excluding ex
                JOIN bps_bundleitem bb on ex.item_id = bb.item_id
                JOIN bps_rankinggroup br on bb.group_id = br.group_id
                WHERE br.rule_id = ?;"
            );
            $delete_stmt->execute(array($rule_id));
            $insert_stmt = $db->prepare("INSERT INTO `studip`.`bps_bundleitem_excluding` (`item_id`, `excl_item_id`) VALUES (?, ?)");
            foreach ($decoded_request as $i => $item) {
                foreach ($item as $excl) {
                    $insert_stmt->execute(array($i, $excl));
                }
            }
        }

        $stmt = $db->prepare("SELECT ex.* FROM bps_bundleitem_excluding ex
                                        JOIN bps_bundleitem bb on ex.excl_item_id = bb.item_id
                                        JOIN bps_rankinggroup br on bb.group_id = br.group_id
                                        WHERE br.rule_id = ?;");
        $stmt->execute(array($rule_id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = [];
        foreach ($result as $item) {
            $output[$item['item_id']][] = $item['excl_item_id'];
        }
        $this->render_text(json_encode($output));
    }

}
