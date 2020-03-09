<?php


class OverviewController extends PluginController
{
    public function index_action() {
        PageLayout::setTitle(_('Laufende präferenzbasierte überschneidungsfreie Anmeldungen'));
        PageLayout::setTabNavigation('/browse');
        Navigation::activateItem('/browse/my_courses/bps_overview');



        $db = DBManager::get();
        $stmt = $db->prepare("SELECT c.set_id, c.name, b.distribution_time, bbr.group_id, br.group_name, bbc.item_id, bbc.seminar_id, bbr.priority FROM bps_bundleitem_ranking bbr
            JOIN bps_bundleitem_course bbc on bbr.item_id = bbc.item_id
            JOIN bps_rankinggroup br on bbr.group_id = br.group_id
            JOIN bpsadmissions b on br.rule_id = b.rule_id
            JOIN courseset_rule cr on b.rule_id = cr.rule_id
            JOIN coursesets c on cr.set_id = c.set_id
            WHERE bbr.user_id = ? AND b.distribution_done = 0
            ORDER BY br.group_id, bbr.priority, c.name;");
        $stmt->execute([array($GLOBALS['user']->id)]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->waiting = [];
        foreach ($result as $row) {
            $this->waiting[$row['set_id']]['set_name'] = $row['name'];
            $this->waiting[$row['set_id']]['distribution_time'] = $row['distribution_time'];
            $this->waiting[$row['set_id']]['groups'][$row['group_id']]['group_id'] = $row['group_id'];
            $this->waiting[$row['set_id']]['groups'][$row['group_id']]['group_name'] = $row['group_name'];
            $this->waiting[$row['set_id']]['groups'][$row['group_id']]['items'][$row['item_id']]['item_id'] = $row['item_id'];
            $this->waiting[$row['set_id']]['groups'][$row['group_id']]['items'][$row['item_id']]['priority'] = $row['priority'];
            $this->waiting[$row['set_id']]['groups'][$row['group_id']]['items'][$row['item_id']]['courses'][$row['seminar_id']]['seminar_id'] = $row['seminar_id'];
            $sem = Seminar::GetInstance($row['seminar_id']);
            $this->waiting[$row['set_id']]['groups'][$row['group_id']]['items'][$row['item_id']]['courses'][$row['seminar_id']]['seminar_name'] = $sem->getName();
        }

        if (empty($this->waiting)) {
            PageLayout::postInfo(_('Im Moment haben Sie keine laufenden Anmeldungen im präferenzbasierten überschneidungsfreien Verteilverfahren.'));
        }
    }
}