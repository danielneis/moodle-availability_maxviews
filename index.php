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
 * Max views overrides.
 *
 * @package availability_maxviews
 * @copyright 2023 Daniel Neis Araujo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
$context = context_course::instance($courseid);
$ctx = context_system::instance();
require_capability('availability/maxviews:override', $context);

$str = get_string('overrides', 'availability_maxviews');

$url = new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $courseid]);

$PAGE->set_context($ctx);
$PAGE->set_url($url);
$PAGE->set_title($str . ' - ' . $SITE->fullname);
$PAGE->set_heading($str);

$output = $PAGE->get_renderer('availability_maxviews');

$index = new \availability_maxviews\output\index($courseid);

echo $output->header(),
     $output->render($index),
     $output->footer();
