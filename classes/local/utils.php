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

namespace availability_maxviews\local;

use availability_maxviews\condition;
use cm_info;
use core\log\reader;
use core\log\sql_reader;
use core_availability\info_module;
use core_availability\tree;
use stdClass;

/**
 * Class utils
 *
 * @package    availability_maxviews
 * @copyright  2026 Mohammad Farouk <phun.for.physics@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Return the count of views and the limits for a given user.
     * @param int $contextid
     * @param int $viewlimit the original view limit
     * @param int $thingid cmid or sectionid
     * @param int $userid if null it will refer to the current user.
     * @return array{viewscount: int, viewslimit: int}
     */
    public static function get_views_count_limit(int $contextid, int $viewslimit, int $thingid, string $kind = 'cm', ?int $userid = null) {
        global $DB;
        if (empty($userid)) {
            global $USER;
            $userid = $USER->id;
        }
        // Check the type of overriding.
        $type = get_config('availability_maxviews', 'overridetype');

        $where = 'contextid = :context AND userid = :userid AND crud = :crud';
        $params = ['context' => $contextid, 'userid' => $userid, 'crud' => 'r'];

        // The table should contain a field that determine if the 'cmid' is for section or course module.
        if ($kind === 'cm' // Till now the table only contains data of cms, and it might be conflicts in ids.
        && $override = $DB->get_record('availability_maxviews', ['cmid' => $thingid, 'userid' => $userid])) {
            if (!empty($override->lastreset)) {

                $where .= ' AND timecreated >= :lastreset';
                $params['lastreset'] = $override->lastreset;
            }
            // If there is override, set the new value according to the type of override.
            if (!empty($override->maxviews)) {
                if ($type === 'normal') {
                    $viewslimit = $override->maxviews;
                } else if ($type === 'add') {
                    $viewslimit += $override->maxviews;
                }
            }
        }

        $reader = self::get_event_reader();
        $viewscount = $reader->get_events_select_count($where, $params);

        return [$viewscount, $viewslimit];
    }

    /**
     * Get an instance of sql reader.
     * @return sql_reader
     */
    public static function get_event_reader(): reader {
        static $reader;
        if (!isset($reader)) {
            $logmanager = get_log_manager();
            if (!$readers = $logmanager->get_readers('core\log\sql_reader')) {
                // Should be using 2.8, use old class.
                $readers = $logmanager->get_readers('core\log\sql_select_reader');
            }
            $reader = reset($readers);
        }
        return $reader;
    }
}
