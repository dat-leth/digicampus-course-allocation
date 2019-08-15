<?php


class OverviewController extends PluginController
{
    public function index_action() {
        PageLayout::setTitle('Laufende Anmeldungen im BPS-Verfahren');
        PageLayout::setTabNavigation('/browse');
        Navigation::activateItem('/browse/my_courses/bps_overview');

        PageLayout::postInfo('Im Moment haben Sie keine laufenden Anmeldungen.');
    }
}