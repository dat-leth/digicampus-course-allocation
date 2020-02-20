<?php


namespace BundleAllocation;

use BundleAllocation\BundleItem;

class RankingGroup
{
    public $id = '';
    public $ruleId;
    public $groupName;
    public $minAmountPrios;
    public $bundleItems;

    public function __construct($groupId = '')
    {
        if ($groupId) {
            $this->id = $groupId;
            $this->load();
        } else {
            $this->id = $this->generateId();
            $this->ruleId = '';
            $this->groupName = '';
            $this->minAmountPrios = 0;
            $this->bundleItems = [];
        }

        return $this;
    }

    public function setAdmissionRuleId($ruleId)
    {
        $this->ruleId = $ruleId;
        return $this;
    }

    public function getName()
    {
        return $this->groupName;
    }

    public function setName($newName)
    {
        $this->groupName = $newName;
        return $this;
    }

    public function getMinAmountPrios()
    {
        return $this->minAmountPrios;
    }

    public function setMinAmountPrios($newAmount)
    {
        $this->minAmountPrios = $newAmount;
        return $this;
    }

    public function getBundleItems()
    {
        return $this->bundleItems;
    }


    public function addBundleItem($bundleItem)
    {
        $this->bundleItems[$bundleItem->getId()] = $bundleItem;
        return $this;
    }

    public function removeBundleItemById($bundleItemId)
    {
        unset($this->bundleItems[$bundleItemId]);
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }



    public function load()
    {
        $db = \DBManager::get();
        $stmt = $db->prepare("SELECT `rule_id`, `group_name`, `min_amount_prios` FROM `bps_rankinggroup` 
            WHERE `group_id` = ?;"
        );
        $stmt->execute([$this->id]);

        if ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->ruleId = $result['rule_id'];
            $this->minAmountPrios = $result['min_amount_prios'];
            $this->groupName = $result['group_name'];
            $this->bundleItems = [];

            $bundleItemStmt = $db->prepare("SELECT `item_id` FROM `bps_bundleitem` WHERE `group_id` = ?");
            $bundleItemStmt->execute([$this->id]);
            while ($itemsResult = $bundleItemStmt->fetch(\PDO::FETCH_ASSOC)) {
                $item = new BundleItem($itemsResult['item_id']);
                $this->bundleItems[$item->getId()] = $item;
            }
        }
    }

    public function store()
    {
        if (!$this->id) {
            $this->id = $this->generateId();
        }
        $db = \DBManager::get();
        $stmt = $db->prepare("INSERT INTO `bps_rankinggroup` (`group_id`, `rule_id`, `group_name`, `min_amount_prios`) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                `group_id` = VALUES(`group_id`), 
                `rule_id` = VALUES(`rule_id`), 
                `group_name` = VALUES(`group_name`), 
                `min_amount_prios` = VALUES(`min_amount_prios`);"
        );
        $stmt->execute([$this->id, $this->ruleId, $this->groupName, $this->minAmountPrios]);

        $item_ids  = implode("', '", array_keys($this->bundleItems));
        $cleanupStmt = $db->prepare("DELETE FROM `bps_bundleitem` WHERE `group_id` = ? AND `item_id` NOT IN ('{$item_ids}');");
        $cleanupStmt->execute([$this->id]);

        foreach ($this->bundleItems as $item) {
            $item->store();
        }
    }

    /**
     * Generate a new unique ID.
     *
     * @return string
     */
    public function generateId()
    {
        do {
            $newid = md5(uniqid(get_class($this) . microtime(), true));
            $id = \DBManager::get()->fetchColumn("SELECT `group_id`
                FROM `bps_rankinggroup` WHERE `group_id`=?", [$newid]);
        } while ($id);
        return $newid;
    }
}