<?php


class ConfigController extends PluginController
{
    public function index_action()
    {
        PageLayout::setTitle('Bundle Allocation verwalten');
        PageLayout::setTabNavigation('/tools');
        Navigation::activateItem('/tools/coursesets/bps_config');
    }

    public function courses_capacity_action($rule_id)
    {
        # TODO: POST Get / POST Set Admission Turnout for List of Courses
        # Verify courses in rule id
        # Set Admission Turnout for Courses class ->store()
    }

    public function ranking_groups_action($rule_id)
    {
        $this->set_content_type('application/json');
        $this->render_text($rule_id);
        # TODO: GET - currently saved ranking groups of courseset/rule
        # TODO: POST - save ranking groups configuration of courseset/rule
    }

    public function ranking_items_action($rule_id)
    {
        $this->set_content_type('application/json');
        $this->render_text($rule_id);
    }
}
