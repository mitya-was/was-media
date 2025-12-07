<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:14
 */

namespace Utils\SendGrid;


class ASM implements \jsonSerializable {
    private
        $group_id,
        $groups_to_display;

    public function setGroupId($group_id) {
        $this->group_id = $group_id;
    }

    public function getGroupId() {
        return $this->group_id;
    }

    public function setGroupsToDisplay($group_ids) {
        $this->groups_to_display = $group_ids;
    }

    public function getGroupsToDisplay() {
        return $this->groups_to_display;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'group_id'          => $this->getGroupId(),
                'groups_to_display' => $this->getGroupsToDisplay()
            ]
        );
    }
}