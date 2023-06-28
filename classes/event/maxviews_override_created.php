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
 * Plugin event classes are defined here.
 *
 * @package     availability_maxviews
 * @copyright   2023 Mo Farouk <phun.for.physics@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_maxviews\event;

/**
 * The view event class.
 *
 * @package    availability_maxviews
 * @copyright  2023 Mo Farouk <phun.for.physics@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class maxviews_override_created extends \core\event\base {

    // For more information about the Events API, please visit:
    // https://docs.moodle.org/dev/Event_2.
    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'availability_maxviews';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_maxviews_override_created', 'availability_maxviews');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $a = new \stdClass;
        $a->relateduserid = $this->relateduserid;
        $a->userid = $this->userid;
        $a->cmid = $this->other['cmid'];
        $a->type = $this->other['type'];
        $reset = $this->other['reset'];
        if ($reset) {
            $a->reset = get_string('resetyes', 'availability_maxviews');
        } else {
            $a->reset = get_string('resetno', 'availability_maxviews');
        }
        return get_string('event_maxviews_override_description', 'availability_maxviews', $a);
    }
}
