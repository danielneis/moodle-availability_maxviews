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
$id = optional_param('id', 0, PARAM_INT);

require_login($courseid);

$ctx = context_system::instance();
require_capability('moodle/course:manageactivities', $ctx);

if ($id) {
    $str = get_string('newoverride', 'availability_maxviews');
} else {
    $str = get_string('editingoverride', 'availability_maxviews');
}
$url = new moodle_url('/availability/condition/maxviews/override.php', ['courseid' => $courseid]);

$PAGE->set_context($ctx);
$PAGE->set_url($url);
$PAGE->set_title($str . ' - ' . $SITE->fullname);
$PAGE->set_heading($str);

$form = new \availability_maxviews\form\override($url, ['courseid' => $courseid]);
if ($id) {
    $form->set_data($DB->get_record('availability_maxviews', ['id' => $id]));
} else {
    $form->set_data(['courseid' => $courseid]);
}

if ($form->is_cancelled()) {
    redirect(new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $courseid]));
} else if ($data = $form->get_data()) {
    if ($id) {
        $record = (object)[
            'id' => $id,
            'maxviews' => $data->maxviews,
        ];
        if (!empty($data->resetviews)) {
            $record->lastreset = time();
        }

        $DB->update_record('availability_maxviews', $record);
        $msg = get_string('overrideupdated', 'availability_maxviews');
    } else {
        $record = (object)[
            'courseid' => $data->courseid,
            'cmid' => $data->cmid,
            'userid' => $data->userid,
            'maxviews' => $data->maxviews,
            'lastreset' => empty($data->resetviews) ? 0 : time(),
        ];
        $DB->insert_record('availability_maxviews', $record);
        $msg = get_string('overrideadded', 'availability_maxviews');
    }
    redirect(new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $courseid]), $msg);
} else {
    echo $OUTPUT->header(),
         $form->render(),
         $OUTPUT->footer();
}
