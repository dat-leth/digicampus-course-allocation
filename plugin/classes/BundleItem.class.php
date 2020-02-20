<?php


namespace BundleAllocation;


class BundleItem
{
    public $id = '';
    public $groupId;
    public $courses;

    public function __construct($itemId = '')
    {
        if ($itemId) {
            $this->id = $itemId;
            $this->load();
        } else {
            $this->id = $this->generateId();
            $this->groupId = '';
            $this->courses = [];
        }

        return $this;
    }

    public function addCourse($courseId)
    {
        $this->courses[$courseId] = true;
        return $this;
    }

    public function removeCourse($courseId)
    {
        unset($this->courses[$courseId]);
        return $this;
    }

    public function hasCourse($courseId)
    {
        return $this->courses[$courseId] === true;
    }

    public function setCourses($newCourseIds)
    {
        $this->courses = [];
        foreach ($newCourseIds as $id) {
            $this->addCourse($id);
        }
        return $this;
    }

    public function getCourses()
    {
        return $this->courses;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRankingGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function load()
    {
        $db = \DBManager::get();
        $stmt = $db->prepare("SELECT `group_id` FROM `bps_bundleitem` WHERE `item_id` = ?;");
        $stmt->execute([$this->id]);

        if ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->groupId = $result['group_id'];
            $this->courses = [];

            $courseStmt = $db->prepare("SELECT `seminar_id` FROM `bps_bundleitem_course` WHERE `item_id` = ?;");
            $courseStmt->execute([$this->id]);
            while ($course = $courseStmt->fetch(\PDO::FETCH_ASSOC)) {
                $this->courses[$course['seminar_id']] = true;
            }
        }
    }

    public function store()
    {
        if (!$this->id) {
            $this->id = $this->generateId();
        }
        $db = \DBManager::get();
        $stmt = $db->prepare("INSERT IGNORE INTO `bps_bundleitem` (`item_id`, `group_id`) VALUES (?, ?);");
        $stmt->execute([$this->id, $this->groupId]);

        $course_ids  = implode("', '", array_keys($this->courses));
        $cleanupStmt = $db->prepare("DELETE FROM `bps_bundleitem_course`
            WHERE `item_id` = ? AND `seminar_id` NOT IN ('{$course_ids}');"
        );
        $cleanupStmt->execute([$this->id]);

        $courseStmt = $db->prepare("INSERT IGNORE INTO `bps_bundleitem_course` (`item_id`, `seminar_id`) VALUES (?, ?);");
        foreach (array_keys($this->courses) as $courseId) {
            $courseStmt->execute([$this->id, $courseId]);
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
            $id = \DBManager::get()->fetchColumn("SELECT `item_id`
                FROM `bps_bundleitem` WHERE `item_id`=?", [$newid]);
        } while ($id);
        return $newid;
    }

}