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

    public function index_action()
    {
        PageLayout::setTitle('Bundle Allocation verwalten');
        PageLayout::setTabNavigation('/tools');
        Navigation::activateItem('/tools/coursesets/bps_config');
    }
}