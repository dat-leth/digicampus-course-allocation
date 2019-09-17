<?php

require_once __DIR__ . '/routes/BundleAllocationRoutes.class.php';

class BundleAllocationPlugin extends StudIPPlugin implements SystemPlugin, RESTAPIPlugin
{
    /**
     * BundleAllocationPlugin constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->addNavigation();
        $this->overrideApplyLink();
    }


    public function getRouteMaps()
    {
        return new BundleAllocationRoutes();
    }

    private function addNavigation()
    {
        if (!$GLOBALS['perm']->have_perm('admin')) {
            $studentPrioNav = new Navigation('Laufende Anmeldungen');
            $studentPrioNav->setURL(PluginEngine::getURL($this, [], 'overview/index'));
            Navigation::addItem('/browse/my_courses/bps_overview', $studentPrioNav);
        }
    }

    private function overrideApplyLink()
    {
        if (match_route('dispatch.php/course/details')) {
            $course_id = Request::option('sem_id');
            if (empty($course_id)) {
                $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $path = rtrim($path, '/');
                $pathTokens = explode('/', $path);
                $course_id = array_slice($pathTokens, -1)[0];
            }
            $this->course = Course::find($course_id);
            if (!$this->course) {
                $course_id = $GLOBALS['SessionSeminar'];
                $this->course = Course::find($course_id);
            }
            if ($this->course) {
                $this->sem = new Seminar($this->course);
                $enrolment_info = $this->sem->getEnrolmentInfo($GLOBALS['user']->id);
                $courseset = $this->sem->getCourseSet();
                if (!is_null($courseset)) {
                    $hasRule = $courseset->hasAdmissionRule('BundleAllocationAdmission');
                }
                if (!in_array($enrolment_info['cause'], ['member', 'root', 'courseadmin']) && $hasRule) {
                    $js = <<<EOT
jQuery('document').ready(function() {
    document.querySelector("[href*='dispatch.php/course/enrolment/apply']").href = STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/bundleallocationplugin/enrollment/apply/$course_id'
});
EOT;
                    PageLayout::addHeadElement('script', array('type' => 'text/javascript'), $js);
                }
            }
        }
    }
}