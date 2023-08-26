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
$context = context_course::instance($courseid);

require_login($courseid);
require_capability('availability/maxviews:override', $context);
// Check the type of overriding.
$type = get_config('availability_maxviews', 'overridetype');

if ($id) {
    $str = get_string('newoverride', 'availability_maxviews');
} else {
    $str = get_string('editingoverride', 'availability_maxviews');
}
$url = new moodle_url('/availability/condition/maxviews/override.php', ['courseid' => $courseid]);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($str . ' - ' . $SITE->fullname);
$PAGE->set_heading($str);

$form = new \availability_maxviews\form\override($url, ['courseid' => $courseid]);
if ($id) {
    $recordold = $DB->get_record('availability_maxviews', ['id' => $id]);
    $recordold->userids[] = $recordold->userid;
    $form->set_data($recordold);
} else {
    $form->set_data(['courseid' => $courseid]);
}

if ($form->is_cancelled()) {
    redirect(new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $courseid]));
} else if ($data = $form->get_data()) {

    // Initialize the event data to trigger.
    $eventarray = [
        'context' => $context,
        'objectid' => $id,
        'userid' => $USER->id,
        'courseid' => $courseid,
        'other' => ['cmid' => $data->cmid],
        ];

    if ($id) { // Edit existence override.
        $key = array_key_first($data->userids);
        $userid = $data->userids[$key];

        $record = (object)[
            'id' => $id,
            'maxviews' => $data->maxviews,
            'userid' => $userid,
            'overriderid' => $USER->id,
            'timeupdated' => time()
        ];

        if (!empty($data->resetviews)) {
            $record->lastreset = time();
            $eventarray['other']['reset'] = true;
        } else {
            $eventarray['other']['reset'] = false;
        }

        $DB->update_record('availability_maxviews', $record);
        $eventarray['relateduserid'] = $userid;
        $eventarray['other']['type'] = 'updated';

        $event = \availability_maxviews\event\maxviews_override_updated::create($eventarray);

        $msg = get_string('overrideupdated', 'availability_maxviews');
    } else { // Add new overrides.
        // Modifying for multiple users change.
        foreach ($data->userids as $userid) {
            $record = [
                'courseid' => $data->courseid,
                'cmid' => $data->cmid,
                'userid' => $userid,
            ];

            $newrecord = [
                'maxviews' => $data->maxviews,
                'overriderid' => $USER->id,
                'timeupdated' => time(),
            ];

            if (!empty($data->resetviews)) {
                $newrecord['lastreset'] = time();
                $eventarray['other']['reset'] = true;
            } else {
                $newrecord['lastreset'] = 0;
                $eventarray['other']['reset'] = false;
            }

            $eventarray['relateduserid'] = $userid;
            // Prevent duplication of records for same user which makes a conflict error when calling get_record() later.
            if ($oldrecord = $DB->get_record('availability_maxviews', $record)) {
                $newrecord['id'] = $oldrecord->id;

                if ($type == 'add') {
                    // Adding the old maxviews.
                    $newrecord['maxviews'] += $oldrecord->maxviews;
                }

                // Check if there is old reset first.
                $newrecord['lastreset'] = max($newrecord['lastreset'], $oldrecord->lastreset);
                $DB->update_record('availability_maxviews', (object)$newrecord);

                $eventarray['other']['type'] = 'updated';
                $event = \availability_maxviews\event\maxviews_override_updated::create($eventarray);

            } else {
                $newrecord['timecreated'] = time();
                $recordobject = (object)array_merge($record, $newrecord);
                $DB->insert_record('availability_maxviews', $recordobject);

                $eventarray['other']['type'] = 'created';
                $event = \availability_maxviews\event\maxviews_override_created::create($eventarray);

            }
        }
        $msg = get_string('overrideadded', 'availability_maxviews');
    }

    // Trigger the event.
    $event->trigger();
    redirect(new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $courseid]), $msg);
} else {
    echo $OUTPUT->header(),
         $form->render(),
         $OUTPUT->footer();
}
