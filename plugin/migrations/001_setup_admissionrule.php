<?php

class SetupAdmissionRule extends Migration
{
    /**
     *
     */
    function up()
    {
        $db = DBManager::get();
        $db->exec("CREATE TABLE IF NOT EXISTS `bpsadmissions` (
  `rule_id` varchar(32) NOT NULL,
  `message` text NOT NULL,
  `distribution_time` int(11) NOT NULL DEFAULT 0,
  `job_id` varchar(36),
  `distribution_done` boolean NOT NULL DEFAULT FALSE,
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
)");

        $path = 'public/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/admissionrule';
        $mkdate = time();
        $db->exec("INSERT INTO `admissionrules` (`id`, `ruletype`, `active`, `mkdate`, `path`) VALUES (NULL, 'BundleAllocationAdmission', '1', {$mkdate}, '{$path}');");

        SimpleORMap::expireTableScheme();
    }

    /**
     *
     */
    function down()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE `bpsadmissions`;");
        $db->exec("DELETE FROM `courseset_rule` WHERE `type`='BundleAllocationAdmission';");
        $db->exec("DELETE FROM `admissionrules` WHERE `ruletype`='BundleAllocationAdmission'");
        SimpleORMap::expireTableScheme();
    }

}