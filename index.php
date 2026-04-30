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

$ctx = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $ctx);

$str = get_string('pluginname', 'availability_maxviews');

$url = new moodle_url('/availability/condition/maxviews/index.php');

$PAGE->set_context($ctx);
$PAGE->set_url($url);
$PAGE->set_title($str . ' - ' . $SITE->fullname);
$PAGE->set_heading($str);

$output = $PAGE->get_renderer('availability_maxviews');

$index = new \availability_maxviews\output\index($courseid);

$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$thingnode = $coursenode->add(
    get_string('pluginname', 'availability_maxviews'),
    new moodle_url('/availability/condition/maxview/index.php')
);
$thingnode->make_active();

echo $output->header(),
     $output->render($index),
     $output->footer();
