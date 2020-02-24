<?php

class SetupBpsDatabase extends Migration
{
    /**
     *
     */
    function up()
    {
        $db = DBManager::get();
        $db->exec("CREATE TABLE IF NOT EXISTS `bps_rankinggroup` (
            `group_id` varchar(32) NOT NULL,
            `rule_id` varchar(32) NOT NULL,
            `group_name` varchar(255) NOT NULL,
            `min_amount_prios` int NOT NULL,
            PRIMARY KEY (`group_id`),
            FOREIGN KEY (`rule_id`) REFERENCES bpsadmissions(rule_id) ON DELETE CASCADE   
        );");
        $db->exec("CREATE TABLE IF NOT EXISTS bps_bundleitem (
            item_id varchar(32) NOT NULL,
            group_id varchar(32) NOT NULL,
            PRIMARY KEY (item_id),
            FOREIGN KEY (group_id) REFERENCES bps_rankinggroup(group_id) ON DELETE CASCADE 
        );");
        $db->exec("CREATE TABLE IF NOT EXISTS bps_bundleitem_course (
            item_id varchar(32) NOT NULL,
            seminar_id varchar(32) NOT NULL,
            PRIMARY KEY (item_id, seminar_id),
            FOREIGN KEY (item_id) REFERENCES bps_bundleitem(item_id) ON DELETE CASCADE
        );");
        $db->exec("CREATE TABLE IF NOT EXISTS bps_bundleitem_ranking (
            user_id varchar(32) NOT NULL,
            group_id varchar(32) NOT NULL,
            item_id varchar(32) NOT NULL,
            priority int NOT NULL DEFAULT 0,
            mkdate int NOT NULL DEFAULT 0,
            chdate int NOT NULL DEFAULT 0,
            PRIMARY KEY (user_id, group_id, item_id),
            FOREIGN KEY (group_id) REFERENCES bps_rankinggroup(group_id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES bps_bundleitem(item_id) ON DELETE CASCADE 
        );");
        $db->exec("CREATE TABLE IF NOT EXISTS bps_prelim_alloc (
            user_id varchar(32) NOT NULL,
            group_id varchar(32) NOT NULL,
            item_id varchar(32) NOT NULL,
            seminar_id varchar(32) NOT NULL,
            priority int NOT NULL,
            waitlist bool NOT NULL,
            PRIMARY KEY (user_id, group_id, item_id, seminar_id),
            FOREIGN KEY (item_id) REFERENCES bps_bundleitem(item_id) ON DELETE CASCADE
        )");
        SimpleORMap::expireTableScheme();
    }

    /**
     *
     */
    function down()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE `bps_bundleitem`");
        $db->exec("DROP TABLE `bps_bundleitem_course`");
        $db->exec("DROP TABLE `bps_bundleitem_ranking`");
        $db->exec("DROP TABLE `bps_rankinggroup`");
        $db->exec("DROP TABLE `bps_prelim_alloc`");
        SimpleORMap::expireTableScheme();
    }

}