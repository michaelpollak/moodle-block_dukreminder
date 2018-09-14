<?php

// https://docs.moodle.org/dev/Restore_2.0_for_developers#Introduction

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block_dukreminder
 * @category    backup
 * @copyright   2018 michaelpollak {@link http://michaelpollak.org}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_dukreminder block
 */

/**
 * Structure step to restore one dukreminder block
 */
class restore_dukreminder_block_structure_step extends restore_structure_step {

    protected function define_structure() {

        $userinfo = $this->get_setting_value('users');
        $paths = array();
        $paths[] = new restore_path_element('dukreminder', '/block/dukreminder');
        // Restore mailssent only if we want userinfos.
        if ($userinfo) {
            $paths[] = new restore_path_element('dukreminder_mailssent', '/block/dukreminder/mailssents/mailssent');
        }
        return $paths;
    }

    protected function process_dukreminder($data) {
        global $DB;
        $userinfo = $this->get_setting_value('users');

        $data = (object)$data;
        $oldid = $data->id;
        $oldcourseid = $data->courseid;
        $data->courseid = $this->get_courseid(); // Courseid needs to be updated.

        // Set mailssent and sent to 0.
        if (!$userinfo) {
            $data->sent = 0;
            $data->mailssent = 0;
        }

        // Check criteria and update.
        if(isset($data->criteria)) {
            // These can just be used as is I think.
            $defaultcriteria = array(250000, 250001, 250002, 250003, 250004);
            if(!in_array($data->criteria, $defaultcriteria)) {
                // Can't restore/map criteria (yet). Set to 0 and disable reminder.
                $data->status = 1;
                $data->criteria = 0;
            }
        }

        // Restore to_groups as good as we can.
        $groupnumbers = explode(';', $data->to_groups); // These are multiple groupids seperated by ;.
        $newgroupnumbers = array();
        foreach($groupnumbers as $groupnumber) {
            $newgroupnumbers[] = $this->get_mappingid('group', $groupnumber);
        }
        $data->to_groups = implode(';', $newgroupnumbers);

        // Insert the dukreminder record
        $newitemid = $DB->insert_record('block_dukreminder', $data);
        $this->set_mapping('dukreminder', $oldid, $newitemid); // Set mapping manual for get_new_parentid.
    }

    protected function process_dukreminder_mailssent($data) {
        global $DB;
        $data = (object)$data;
        $oldid = $data->id;
        $data->reminderid = $this->get_new_parentid('dukreminder');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('block_dukreminder_mailssent', $data);
    }
}
