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
 * Plugin lib.
 *
 * @package availability_maxviews
 * @copyright 2023 Daniel Neis Araujo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function availability_maxviews_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    global $PAGE;

    if (has_capability('availability/maxviews:override', $context)) {
                // Find the course settings node using the 'courseadmin' key.

        $url = new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $course->id]);
        $pluginname = get_string('pluginname', 'availability_maxviews');
        $node = navigation_node::create(
            $pluginname,
            $url,
            navigation_node::NODETYPE_LEAF,
            null,
            'maxviews',
            new pix_icon('i/report', $pluginname, 'availability_maxviews')
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $node->make_active();
        }
        $navigation->add_node($node);
    }
}
