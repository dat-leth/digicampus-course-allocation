<?php

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
        PageLayout::setTitle('Übungen zu Informatik 2 - Präferenzerhebung');
        # TODO:
        #   If rule.distribution_time < time:
        #   Get ranking group and all bundle items of this group
        #   Get previously given preferences
        #   Encode in JSON in view
        #   If POSTed decode JSON with preferences and save to database
    }
}