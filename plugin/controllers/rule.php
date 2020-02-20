<?php

require_once __DIR__ . '/../classes/RankingGroup.class.php';
require_once __DIR__ . '/../classes/BundleItem.class.php';

class RuleController extends PluginController
{
    /**
     * @param $action
     * @param $args
     * @return bool|void
     * @throws Trails_Exception
     * @throws Trails_SessionRequiredException
     * @throws AccessDeniedException
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        if (!($GLOBALS['perm']->have_perm('admin') || ($GLOBALS['perm']->have_perm('dozent') && get_config('ALLOW_DOZENT_COURSESET_ADMIN')))) {
            throw new AccessDeniedException();
        }
    }

    public function add_ranking_group_action()
    {
        $group = new BundleAllocation\RankingGroup();
        $group->setAdmissionRuleId(Request::option('rule_id'));
        $group->setName(Request::get('group_name', ''));

        $serialized = ObjectBuilder::export($group);
        $serialized['bundleItems'] = (object) [];

        $this->response->add_header('Content-Type', 'application/json');
        $this->render_text(json_encode($serialized));
    }

    public function add_bundle_item_action()
    {
        $item = new BundleAllocation\BundleItem();
        $item->setRankingGroupId(Request::option('group_id'));

        $serialized = ObjectBuilder::export($item);
        $serialized['courses'] = (object) [];

        $this->response->add_header('Content-Type', 'application/json');
        $this->render_text(json_encode($serialized));
    }

    public function course_infos_action()
    {
        $data = json_decode(trim(file_get_contents("php://input")), true);
        $response = [];
        if (isset($data['course_ids'])) {
            foreach ($data['course_ids'] as $course_id) {
                $sem = Seminar::GetInstance($course_id);
                $response[$course_id]['course_id'] = $course_id;
                $response[$course_id]['name'] = $sem->getFullname('number-name-semester');
                $response[$course_id]['capacity'] = (int)$sem->admission_turnout;
                $response[$course_id]['times_rooms'] = $sem->getDatesTemplate('dates/seminar_html', ['show_room' => true]);
            }
        }

        $this->response->add_header('Content-Type', 'application/json');
        $this->render_text(json_encode($response));
    }
}