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
 * Date condition.
 *
 * @package availability_maxviews
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_maxviews;

defined('MOODLE_INTERNAL') || die();

/**
 * maxviews condition.
 *
 * @package availability_maxviews
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        if (!isset($structure->viewslimit)) {
            $this->viewslimit = 0;
        } else {
            $this->viewslimit = (int)$structure->viewslimit;
        }
    }

    public function save() {
        $result = (object)array('type' => 'maxviews');
        if ($this->viewlimit) {
            $result->viewslimit = $this->viewslimit;
        }
        return $result;
    }

    /**
     * Returns a JSON object which corresponds to a condition of this type.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * @return stdClass Object representing condition
     */
    public static function get_json($viewslimit = 10) {
        return (object)array('type' => 'maxviews', 'viewslimit' => $viewslimit);
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {

        $logmanager = get_log_manager();
        if (!$readers = $logmanager->get_readers('core\log\sql_reader')) {
            // Should be using 2.8, use old class.
            $readers = $logmanager->get_readers('core\log\sql_select_reader');
        }
        $reader = array_pop($readers);
        $context = $info->get_context();
        $viewscount = $reader->get_events_select_count('contextid = :context AND userid = :userid AND crud = :crud',
                                                  array('context' => $context->id, 'userid' => $userid, 'crud' => 'r'));
        return ($viewscount < $this->viewslimit);

    }

    public function get_description($full, $not, \core_availability\info $info) {
        return $this->get_either_description($not, false);
    }
    /**
     * Shows the description using the different lang strings for the standalone
     * version or the full one.
     *
     * @param bool $not True if NOT is in force
     * @param bool $standalone True to use standalone lang strings
     */
    protected function get_either_description($not, $standalone) {
        return get_string('eitherdescription', 'availability_maxviews', $this->viewslimit);
    }

    protected function get_debug_string() {
        return gmdate('Y-m-d H:i:s');
    }

    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        return true;
    }
}
