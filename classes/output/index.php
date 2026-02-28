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

namespace availability_maxviews\output;

use moodle_url;
use renderable;
use templatable;
use renderer_base;

/**
 * Index renderable class.
 *
 * @package   availability_maxviews
 * @copyright 2023 Daniel Neis Araujo <daniel@adapta.online>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index implements renderable, templatable {

    public $courseid;

    public $reports = [];

    /**
     * Constructor.
     */
    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    public function export_for_template(renderer_base $output) {
        global $DB;

        $type = get_config('availability_maxviews', 'overridetype');
        $typeadd = ($type === 'add');

        $userfieldsapi = \core_user\fields::for_name();
        $usernamefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $sql = 'SELECT m.*, ' . $usernamefields . '
                  FROM {availability_maxviews} m
                  JOIN {user} u
                    ON u.id = m.userid
                 WHERE m.courseid = :courseid';
        $overrides = $DB->get_records_sql($sql, ['courseid' => $this->courseid]);
        $modinfo = get_fast_modinfo($this->courseid);

        foreach ($overrides as $key => $o) {
            if (!isset($modinfo->cms[$o->cmid])) {
                continue;
            }

            $params = ['courseid' => $this->courseid, 'id' => $o->id];
            $overrideurl = new moodle_url('/availability/condition/maxviews/override.php', $params);
            $overrides[$key]->overrideurl = $overrideurl->out(false);
            $params['sesskey'] = sesskey();
            $deleteoverrideurl = new moodle_url('/availability/condition/maxviews/delete.php', $params);
            $overrides[$key]->deleteoverrideurl = $deleteoverrideurl->out(false);
            if (!empty($modinfo->cms[$o->cmid])) {
                $overrides[$key]->coursemodule = $modinfo->cms[$o->cmid]->get_formatted_name();
            } else {
                $overrides[$key]->coursemodule = get_string('deleted');
            }

            $overrides[$key]->userfullname = fullname($o);
            if (!empty($o->overriderid)) {
                // The user that did the override process.
                $overrider = \core_user::get_user($o->overriderid);
            }

            if (!empty($overrider)) {
                $overrides[$key]->overrider = fullname($overrider);
                $overrides[$key]->date = $o->timeupdated;
            } else {
                $overrides[$key]->overrider = '';
                $overrides[$key]->date = '';
            }

        }

        $newoverrideurl = new moodle_url('/availability/condition/maxviews/override.php', ['courseid' => $this->courseid]);
        return (object)[
            'overrides' => array_values($overrides),
            'typeadd' => $typeadd,
            'hasoverrides' => !empty($overrides),
            'newoverrideurl' => $newoverrideurl->out(false),
        ];
    }
}
