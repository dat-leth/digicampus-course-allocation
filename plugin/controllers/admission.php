<?php


class AdmissionController extends PluginController
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

    public function applications_action($coursesetId, $csv = null)
    {
        $courseset = new CourseSet($coursesetId);
        PageLayout::setTitle(sprintf("Anmeldungen zu %s verwalten", $courseset->getName()));

        $db = DBManager::get();

        if ($csv) {
            $stmt = $db->prepare("
                SELECT a.username,
                       a.Nachname,
                       a.Vorname,
                       bbr.group_id,
                       br.group_name,
                       bbr.item_id,
                       bbc.seminar_id,
                       s.name,
                       bbr.priority + 1 as priority,
                       IF(EXISTS(SELECT *
                                 FROM bps_prelim_alloc bpa
                                 WHERE bpa.user_id = bbr.user_id
                                   AND bpa.item_id = bbr.item_id
                                   AND bpa.seminar_id = bbc.seminar_id
                                   AND bpa.waitlist = FALSE), \"TRUE\", \"FALSE\") AS preliminary_allocated,
                       IF(EXISTS(SELECT *
                                 FROM bps_prelim_alloc bpa
                                 WHERE bpa.user_id = bbr.user_id
                                   AND bpa.item_id = bbr.item_id
                                   AND bpa.seminar_id = bbc.seminar_id
                                   AND bpa.waitlist = TRUE), \"TRUE\", \"FALSE\") AS waitlist,
                       bbr.mkdate,
                       bbr.chdate
                FROM bps_bundleitem_ranking bbr
                         JOIN bps_bundleitem_course bbc on bbr.item_id = bbc.item_id
                         JOIN seminare s on bbc.seminar_id = s.Seminar_id
                         JOIN bps_rankinggroup br on bbr.group_id = br.group_id
                         JOIN bpsadmissions b on br.rule_id = b.rule_id
                         JOIN courseset_rule cr on b.rule_id = cr.rule_id
                JOIN auth_user_md5 a on bbr.user_id = a.user_id
                WHERE cr.set_id = ?
                ORDER BY bbr.group_id, bbr.user_id, bbr.priority;"
            );

            $stmt->execute([$coursesetId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $i => $row) {
                $row['mkdate'] = date(DATE_ATOM, $row['mkdate']);
                $row['chdate'] = date(DATE_ATOM, $row['chdate']);
                $result[$i] = array_values($row);
            }

            $captions = ['Nutzername', 'Nachname', 'Vorname', 'Gruppen-ID', 'Gruppen-Name', 'Item-ID', 'Veranstaltungs-ID',
                'Veranstaltungsname', 'Priorität', 'Vorläufig zugeteilt', 'Wartelist', 'Anmeldezeitpunkt', 'Letzte Änderung'];

            $tmpname = md5(uniqid('tmp'));
            if (array_to_csv($result, $GLOBALS['TMP_PATH'] . '/' . $tmpname, $captions)) {
                $this->redirect(
                    FileManager::getDownloadURLForTemporaryFile(
                        $tmpname,
                        'Anmeldungen_' . $courseset->getName() . '.csv'
                    )
                );
                return;
            }
            $this->json = json_encode([]);
            return;
        }

        $rankings_stmt = $db->prepare("SELECT bbr.user_id, bbr.group_id, bbr.item_id, bbr.priority, br.group_name, bbc.seminar_id, bbr.mkdate, bbr.chdate FROM bps_bundleitem_ranking bbr
            JOIN bps_bundleitem_course bbc on bbr.item_id = bbc.item_id
            JOIN bps_rankinggroup br on bbr.group_id = br.group_id
            JOIN bpsadmissions b on br.rule_id = b.rule_id
            JOIN courseset_rule cr on b.rule_id = cr.rule_id
            WHERE cr.set_id = ?
            ORDER BY group_id, user_id, priority;"
        );
        $rankings_stmt->execute([$coursesetId]);
        $result = $rankings_stmt->fetchAll(PDO::FETCH_ASSOC);

        $prelim_stmt = $db->prepare("SELECT br.group_id, bpa.* FROM bps_prelim_alloc bpa
            JOIN bps_bundleitem bb on bpa.item_id = bb.item_id
            JOIN bps_rankinggroup br on bb.group_id = br.group_id
            JOIN bpsadmissions b on br.rule_id = b.rule_id
            JOIN courseset_rule cr on b.rule_id = cr.rule_id
            WHERE cr.set_id = ?;"
        );
        $prelim_stmt->execute([$coursesetId]);
        $prelim_result = $prelim_stmt->fetchAll(PDO::FETCH_ASSOC);

        $avail_courses_stmt = $db->prepare("SELECT br.group_id, bbc.* FROM bps_bundleitem_course bbc
            JOIN bps_bundleitem bb on bbc.item_id = bb.item_id
            JOIN bps_rankinggroup br on bb.group_id = br.group_id
            JOIN bpsadmissions b on br.rule_id = b.rule_id
            JOIN courseset_rule cr on b.rule_id = cr.rule_id
            WHERE cr.set_id = ?;");
        $avail_courses_stmt->execute([$coursesetId]);
        $avail_courses_result = $avail_courses_stmt->fetchAll(PDO::FETCH_ASSOC);
        $seminar_ids = array_unique(array_column($avail_courses_result, 'seminar_id'));
        $courses = [];
        foreach ($seminar_ids as $id) {
            $sem = Seminar::GetInstance($id);
            $courses[$id]['seminar_id'] = $id;
            $courses[$id]['name'] = $sem->getName();
            $courses[$id]['formatted_date'] = $sem->getDatesTemplate('dates/seminar_html', ['show_room' => true]);
            $courses[$id]['capacity'] = $sem->admission_turnout;
        }

        $rankings = [];
        foreach ($result as $row) {
            $rankings[$row['group_id']]['group_id'] = $row['group_id'];
            $rankings[$row['group_id']]['group_name'] = $row['group_name'];
            $rankings[$row['group_id']]['users'][$row['user_id']]['user_id'] = $row['user_id'];
            $rankings[$row['group_id']]['users'][$row['user_id']]['items'][$row['priority']]['item_id'] = $row['item_id'];
            $rankings[$row['group_id']]['users'][$row['user_id']]['items'][$row['priority']]['courses'][] = $courses[$row['seminar_id']];
        }

        if (!empty($rankings)) {
            foreach ($avail_courses_result as $row) {
                $rankings[$row['group_id']]['available_courses'][] = ['item_id' => $row['item_id'], 'course' => $courses[$row['seminar_id']]];
            }
        }

        foreach ($rankings as $g => $group) {
            if (!empty($group['users'])) {
                foreach ($group['users'] as $u => $user) {
                    $user_obj = User::find($user['user_id']);
                    $rankings[$g]['users'][$u]['nachname'] = $user_obj->nachname;
                    $rankings[$g]['users'][$u]['vorname'] = $user_obj->vorname;
                    $rankings[$g]['users'][$u]['username'] = $user_obj->username;
                    $rankings[$g]['users'][$u]['items'] = array_values($user['items']);
                }
                $rankings[$g]['users'] = array_values($rankings[$g]['users']);
            }
        }

        $rankings = array_values($rankings);
        $this->rankings = $rankings;

        $this->prelim = [];
        foreach ($prelim_result as $row) {
            $this->prelim[$row['user_id']]['user_id'] = $row['user_id'];
            $this->prelim[$row['user_id']]['groups'][$row['group_id']]['group_id'] = $row['group_id'];
            $this->prelim[$row['user_id']]['groups'][$row['group_id']]['item_id'] = $row['item_id'];
            $this->prelim[$row['user_id']]['groups'][$row['group_id']]['seminar_id'] = $row['seminar_id'];
            $this->prelim[$row['user_id']]['groups'][$row['group_id']]['priority'] = $row['priority'];
            $this->prelim[$row['user_id']]['groups'][$row['group_id']]['waitlist'] = (bool) $row['waitlist'];
        }
        foreach ($this->prelim as $i => $user) {
            $this->prelim[$i]['groups'] = array_values($user['groups']);
        }
        $this->prelim = array_values($this->prelim);

        $this->setId = $coursesetId;
    }

    public function submit_alloc_action($coursesetId)
    {
        if (Request::isPost()) {
            $request = trim(file_get_contents("php://input"));
            $decoded_request = json_decode($request, true);
            if ($decoded_request === NULL) {
                throw new Trails_Exception(500, 'bad request, could not decode json');
            }

            $db = DBManager::get();
            $stmt = $db->prepare("INSERT INTO `bps_prelim_alloc` (user_id, group_id, item_id, seminar_id, priority, waitlist) 
                VALUES (?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE item_id=VALUES(item_id), seminar_id=VALUES(seminar_id), priority=VALUES(priority), waitlist=VALUES(waitlist);");

            foreach ($decoded_request as $user) {
                $user_id = $user['user_id'];
                foreach ($user['groups'] as $group) {
                    $stmt->execute([$user_id, $group['group_id'], $group['item_id'], $group['seminar_id'], $group['priority'], $group['waitlist']]);
                    if ($stmt <= 0) {
                        throw new Trails_Exception(500, 'could not save allocation to database');
                    }
                }
            }

            $finalize = Request::option('finalize', false) === 'true' ? true : false;
            if ($finalize === true) {
                $waitlist_result = $db->fetchAll("SELECT bpa.user_id, bpa.seminar_id, bpa.priority
                    FROM bps_prelim_alloc bpa
                             JOIN bps_rankinggroup br on bpa.group_id = br.group_id
                             JOIN bpsadmissions b on br.rule_id = b.rule_id
                             JOIN courseset_rule cr on b.rule_id = cr.rule_id
                    WHERE cr.set_id = ? AND bpa.waitlist = TRUE;", [$coursesetId]);
                $waitlist = [];
                foreach ($waitlist_result as $row) {
                    $waitlist[$row['seminar_id']]['users'][] = $row['user_id'];
                    $waitlist[$row['seminar_id']]['prios'][$row['user_id']] = $row['priority'] + 1;
                }
                foreach ($waitlist as $id => $seminar) {
                    $course = Course::find($id);
                    $this->addUsersToWaitlist($seminar['users'], $course, $seminar['prios']);
                }

                $alloc_result = $db->fetchAll("SELECT bpa.user_id, bpa.seminar_id, bpa.priority
                    FROM bps_prelim_alloc bpa
                             JOIN bps_rankinggroup br on bpa.group_id = br.group_id
                             JOIN bpsadmissions b on br.rule_id = b.rule_id
                             JOIN courseset_rule cr on b.rule_id = cr.rule_id
                    WHERE cr.set_id = ? AND bpa.waitlist = FALSE;", [$coursesetId]);
                $alloc = [];
                foreach ($alloc_result as $row) {
                    $alloc[$row['seminar_id']]['users'][] = $row['user_id'];
                    $alloc[$row['seminar_id']]['prios'][$row['user_id']] = $row['priority'] + 1;
                }
                foreach ($alloc as $id => $seminar) {
                    $course = Course::find($id);
                    $this->addUsersToCourse($seminar['users'], $course, $seminar['prios']);
                }

                $db->execute("DELETE bpa FROM bps_prelim_alloc bpa
                                JOIN bps_rankinggroup br on bpa.group_id = br.group_id
                                JOIN bpsadmissions b on br.rule_id = b.rule_id
                                JOIN courseset_rule cr on b.rule_id = cr.rule_id
                            WHERE cr.set_id = ?", [$coursesetId]);
                $db->execute("DELETE bbr FROM bps_bundleitem_ranking bbr
                                JOIN bps_bundleitem_course bbc on bbr.item_id = bbc.item_id
                                JOIN bps_rankinggroup br on bbr.group_id = br.group_id
                                JOIN bpsadmissions b on br.rule_id = b.rule_id
                                JOIN courseset_rule cr on b.rule_id = cr.rule_id
                            WHERE cr.set_id = ? ", [$coursesetId]);
            }

            $this->set_content_type('application/json');
            $this->render_text(json_encode(array('status' => 'success')));
        } else {
            $this->set_status(405);
            $this->set_content_type('application/json');
            $this->render_text(json_encode(array('status' => 'not allowed')));
        }
    }

    /**
     * Adapted from RandomAlgorithm.class.php
     *
     * Add the lucky ones who got a seat to the given course.
     *
     * @param Array  $user_list users to add as members
     * @param Course $course    course to add users to
     * @param int    $prio      user's priority for the given course
     */
    private function addUsersToCourse($user_list, $course, $prio = null)
    {
        $seminar = new Seminar($course);
        foreach ($user_list as $chosen_one) {
            setTempLanguage($chosen_one);
            $message_title = sprintf(_('Teilnahme an der Veranstaltung %s'), $seminar->getName());
            if ($seminar->admission_prelim) {
                if ($seminar->addPreliminaryMember($chosen_one)) {
                    $message_body = sprintf (_('Sie haben bei der Platzvergabe der Veranstaltung **%s** einen vorläufigen Platz erhalten. Die endgültige Zulassung zu der Veranstaltung ist noch von weiteren Bedingungen abhängig, die Sie bitte der Veranstaltungsbeschreibung entnehmen.'),
                        $seminar->getName());
                }
            } else {
                if ($seminar->addMember($chosen_one, 'autor')) {
                    $message_body = sprintf (_("Sie haben bei der Platzvergabe der Veranstaltung **%s** einen Platz erhalten. Ab sofort finden Sie die Veranstaltung in der Übersicht Ihrer Veranstaltungen. Damit sind Sie auch für die Präsenzveranstaltung zugelassen."),
                        $seminar->getName());
                }
            }
            if ($prio) {
                $message_body .= "\n" . sprintf(_("Sie hatten für diese Veranstaltung die Priorität %s gewählt."), $prio[$chosen_one]);
            }
            messaging::sendSystemMessage($chosen_one, $message_title, $message_body);
            restoreLanguage();
        }
    }

    /**
     * Adapted from RandomAlgorithm.class.php
     *
     * Notify users that they couldn't get a seat but are now on the waiting
     * list for a given course.
     *
     * @param Array  $user_list Users to be notified
     * @param Course $course    The course without waiting list
     * @param int    $prio      User's priority for the given course.
     */
    private function addUsersToWaitlist($user_list, $course, $prio = null)
    {
        $maxpos = $course->admission_applicants->findBy('status', 'awaiting')->orderBy('position desc')->val('position');
        foreach ($user_list as $chosen_one) {
            if ($course->getParticipantStatus($chosen_one)) {
                continue;
            }
            $maxpos++;
            $new_admission_member = new AdmissionApplication();
            $new_admission_member->user_id = $chosen_one;
            $new_admission_member->position = $maxpos;
            $new_admission_member->status = 'awaiting';
            try {
                $course->admission_applicants[] = $new_admission_member;
            } catch (InvalidArgumentException $e) {
                Log::DEBUG($e->getMessage());
                continue;
            }
            if ($new_admission_member->store()) {
                setTempLanguage($chosen_one);
                $message_title = sprintf(_('Teilnahme an der Veranstaltung %s'), $course->name);
                $message_body = sprintf(_('Sie haben leider bei der Platzverteilung der Veranstaltung **%s** __keinen__ Platz erhalten. Sie wurden jedoch auf Position %s auf die Warteliste gesetzt. Das System wird Sie automatisch eintragen und benachrichtigen, sobald ein Platz für Sie frei wird.'),
                    $course->name,
                    $maxpos);
                if ($prio) {
                    $message_body .= "\n" . sprintf(_("Sie hatten für diese Veranstaltung die Priorität %s gewählt."), $prio[$chosen_one]);
                }
                messaging::sendSystemMessage($chosen_one, $message_title, $message_body);
                restoreLanguage();
                StudipLog::log('SEM_USER_ADD', $course->id, $chosen_one, 'awaiting', 'Auf Warteliste gelost, Position: ' . $maxpos);
            }
        }
    }
}