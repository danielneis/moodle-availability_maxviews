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
 * Delete override.
 *
 * @package   availability_maxviews
 * @copyright 2023 Daniel Neis Araujo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once(__DIR__.'/classes/event/maxviews_override_deleted.php');

$id = required_param('id', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();
$context = context_course::instance($courseid);
$ctx = context_system::instance();
require_capability('availability/maxviews:override', $context);

if (confirm_sesskey()) {
    $override = $DB->get_record('availability_maxviews', ['id' => $id]);

    if ($confirm) {
        // Initialize the event data to trigger before deleting the record.
        $eventarray = [
            'context' => $context,
            'objectid' => $id,
            'userid' => $USER->id,
            'relateduserid' => $override->userid,
            'courseid' => $courseid,
            'other' => ['cmid' => $override->cmid,
                        'type' => 'deleted',
                        'reset' => false,
                        ],
            ];

        $DB->delete_records('availability_maxviews', ['id' => $id]);
        // Define the event.
        $event = availability_maxviews\event\maxviews_override_deleted::create($eventarray);
        // Trigger the event.
        $event->trigger();
        $url = new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $courseid]);
        redirect($url, get_string('overridedeleted', 'availability_maxviews'));
    } else {

        $user = $DB->get_record('user', ['id' => $override->userid], 'id,firstname,lastname');

        $PAGE->set_context(context_system::instance());
        $PAGE->set_pagelayout('standard');
        $PAGE->set_url(new moodle_url('/availability/condition/maxviews/delete.php'));
        $PAGE->set_title(new lang_string('confirm'));

        echo $OUTPUT->header();

        $optionsyes = ['id' => $id, 'sesskey' => sesskey(), 'confirm' => 1, 'courseid' => $courseid];
        $optionsno = ['courseid' => $courseid];

        $url = new moodle_url('/availability/condition/maxviews/delete.php', $optionsyes);
        $buttoncontinue = new single_button($url, get_string('yes'), 'get');

        $url = new moodle_url('/availability/condition/maxviews/index.php', $optionsno);
        $buttoncancel = new single_button($url, get_string('no'), 'get');

        $message = get_string('confirmdeleteoverride', 'availability_maxviews', $user->firstname . ' ' . $user->lastname);

        echo $OUTPUT->confirm($message, $buttoncontinue, $buttoncancel);
        echo $OUTPUT->footer();
    }
}
