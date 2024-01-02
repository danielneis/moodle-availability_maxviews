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
 * @copyright 2015 Daniel Neis Araujo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_maxviews;

/**
 * maxviews condition.
 *
 * @package availability_maxviews
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    protected $viewslimit;

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        $this->viewslimit = $structure->viewslimit;
    }

    /**
     * Create object to be saved representing this condition.
     */
    public function save() {
        return (object)array('type' => 'maxviews', 'viewslimit' => $this->viewslimit);
    }

    /**
     * Returns a JSON object which corresponds to a condition of this type.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * @param int $viewslimit The limit of views for users
     * @return stdClass Object representing condition
     */
    public static function get_json($viewslimit = 5) {
        return (object)array('type' => 'maxviews', 'viewslimit' => (int)$viewslimit);
    }

    /**
     * Determines whether a particular item is currently available
     * according to this availability condition.
     *
     * @param bool $not Set true if we are inverting the condition
     * @param \core_availability\info $info Item we're checking
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid User ID to check availability for
     * @return bool True if available
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        global $DB;
        // Check the type of overriding.
        $type = get_config('availability_maxviews', 'overridetype');

        $logmanager = get_log_manager();
        if (!$readers = $logmanager->get_readers('core\log\sql_reader')) {
            // Should be using 2.8, use old class.
            $readers = $logmanager->get_readers('core\log\sql_select_reader');
        }
        $reader = array_pop($readers);

        [$cmid, $kind] = $this->get_selfid($info);

        $context = $info->get_context();
        $where = 'contextid = :context AND userid = :userid AND crud = :crud';
        $params = ['context' => $context->id, 'userid' => $userid, 'crud' => 'r'];

        $viewslimit = $this->viewslimit;

        if ($kind == 'cm' // Till now the table only contains data of cms, and it might be conflicts in ids.
        && $override = $DB->get_record('availability_maxviews', ['cmid' => $cmid, 'userid' => $userid])) {
            if (!empty($override->lastreset)) {
                $where .= ' AND timecreated >= :lastreset';
                $params['lastreset'] = $override->lastreset;
            }
            // If there is override, set the new value according to the type of override.
            if (!empty($override->maxviews)) {
                if ($type == 'normal') {
                    $viewslimit = $override->maxviews;
                } else if ($type == 'add') {
                    $viewslimit += $override->maxviews;
                }
            }
        }

        $viewscount = $reader->get_events_select_count($where, $params);
        $allow = ($viewscount < $viewslimit);
        if ($not) {
            $allow = !$allow;
        }

        return $allow;
    }

    /**
     * Obtains a string describing this restriction (whether or not
     * it actually applies).
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param \core_availability\info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_description($full, $not, \core_availability\info $info) {
        global $USER, $DB;
        // Check the type of overriding.
        $type = get_config('availability_maxviews', 'overridetype');
        $logmanager = get_log_manager();
        if (!$readers = $logmanager->get_readers('core\log\sql_reader')) {
            // Should be using 2.8, use old class.
            $readers = $logmanager->get_readers('core\log\sql_select_reader');
        }
        $reader = array_pop($readers);
        $context = $info->get_context();

        [$cmid, $kind] = $this->get_selfid($info);

        $where = 'contextid = :context AND userid = :userid AND crud = :crud';
        $params = ['context' => $context->id, 'userid' => $USER->id, 'crud' => 'r'];

        $viewslimit = $this->viewslimit;

        // The table should contain a field that determine if the 'cmid' is for section or course module.
        if ($kind == 'cm' // Till now the table only contains data of cms, and it might be conflicts in ids.
        && $override = $DB->get_record('availability_maxviews', ['cmid' => $cmid, 'userid' => $USER->id])) {
            if (!empty($override->lastreset)) {

                $where .= ' AND timecreated >= :lastreset';
                $params['lastreset'] = $override->lastreset;
            }
            // If there is override, set the new value according to the type of override.
            if (!empty($override->maxviews)) {
                if ($type == 'normal') {
                    $viewslimit = $override->maxviews;
                } else if ($type == 'add') {
                    $viewslimit += $override->maxviews;
                }
            }
        }
        $viewscount = $reader->get_events_select_count($where, $params);

        $a = new \stdclass();
        $a->viewslimit = $viewslimit;
        $a->viewscount = $viewscount;

        if ($not) {
            return get_string('eithernotdescription', 'availability_maxviews', $a);
        } else {
            return get_string('eitherdescription', 'availability_maxviews', $a);
        }
    }

    /**
     * Return current item ID (cmid or sectionid).
     *
     * @param \core_availability\info $info
     * @return array cmid/sectionid/null
     */
    public function get_selfid(\core_availability\info $info): ?array {

        if ($info instanceof info_module) {
            $cminfo = $info->get_course_module();
            if (!empty($cminfo->id)) {
                 return [$cminfo->id, 'cm'];
            }
        }
        if ($info instanceof info_section) {
            $section = $info->get_section();
            if (!empty($section->id)) {
                return [$section->id, 'section'];
            }

        }
        return [null, ''];
    }

    /**
     * Obtains a representation of the options of this condition as a string,
     * for debugging.
     *
     * @return string Text representation of parameters
     */
    protected function get_debug_string() {
        return gmdate('Y-m-d H:i:s');
    }
}
