<?php

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
 * Defines backup_dukreminder_block class
 *
 * @package     block_dukreminder
 * @category    backup
 * @copyright   2018 michaelpollak {@link http://michaelpollak.org}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/dukreminder/backup/moodle2/backup_dukreminder_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the dukreminder instance
 */
class backup_dukreminder_block_task extends backup_block_task {

    /**
     * No specific settings for this block
     */
    protected function define_my_settings() {
        return array('userinfo');
    }

    /**
     * Defines a backup step to store the instance data in the dukreminder.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_dukreminder_block_structure_step('dukreminder_structure', 'dukreminder.xml'));
    }

    public function get_fileareas() {
        return array(); // No associated fileareas
    }

    public function get_configdata_encoded_attributes() {
        return array(); // No special handling of configdata
    }

    /**
     * No content encoding needed for this activity
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the same content with no changes
     */
    static public function encode_content_links($content) {
        return $content;
    }
}
