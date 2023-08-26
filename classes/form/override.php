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

namespace availability_maxviews\form;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

use moodleform;

/**
 * Override form class.
 *
 * @package   availability_maxviews
 * @copyright 2023 Daniel Neis Araujo <daniel@adapta.online>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override extends moodleform {

    public function definition() {
        global $DB;
        $type = get_config('availability_maxviews', 'overridetype');
        $mform = $this->_form;

        $id = $this->_customdata['id'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $modinfo = get_fast_modinfo($this->_customdata['courseid']);
        $options = ['' => ''];

        foreach ($modinfo->cms as $i) {
            // Filter course modules by that only has maxviews conditions.
            if (!empty($i->availability)) {
                $av = json_decode($i->availability);
                foreach ($av->c as $condition) {
                    if ($condition->type !== 'maxviews') {
                        continue;
                    }
                }
            } else {
                continue;
            }

            // Adding the filtered modules to the options array.
            $options[$i->id] = $i->name;
        }

        $mform->addElement(
            'autocomplete',
            'cmid',
            get_string('coursemodule', 'availability_maxviews'),
            $options,
            [],
        );
        $mform->addRule('cmid', null, 'required', null, 'client');
        $mform->addHelpButton('cmid', 'coursemodule', 'availability_maxviews');

        $options = ['' => ''];
        $users = get_enrolled_users(\context_course::instance($this->_customdata['courseid']));

        // Adding the user's identity fields to make it easier to search for many users.
        $context = \context_course::instance($this->_customdata['courseid']);
        $userfields = \core_user\fields::get_identity_fields($context, true);
        $filtersetting = get_config('availability_maxviews', 'filterusers');
        foreach ($users as $user) {
            // Filter users with those with restrictions only.
            if ($filtersetting === 'filter' &&
            has_capability('moodle/course:ignoreavailabilityrestrictions', $context, $user)) {
                continue;
            }

            $userdata = [];
            $userfullname = fullname($user);
            foreach ($userfields as $userfield) {
                if (isset($user->$userfield)) {
                    $useridentity = $user->$userfield;
                } else {
                    // Extract the name of the user field.
                    $fieldname = str_replace('profile_field_', '', $userfield);
                    // Get the ID of the custom profile field.
                    $fieldid = $DB->get_field('user_info_field', 'id', array('shortname' => $fieldname));
                    // Get the data from the user_info_data table using the field ID and user ID.
                    $useridentity = $DB->get_field('user_info_data', 'data', array('userid' => $user->id, 'fieldid' => $fieldid));
                }

                if (empty($useridentity)) {
                    continue;
                }

                $userdata[] = $useridentity;
            }
            $output = $userfullname.' ('.implode(', ', $userdata).')';
            $options[$user->id] = $output;
        }

        $mform->addElement(
            'autocomplete',
            'userids',
            get_string('participant', 'availability_maxviews'),
            $options,
            ['multiple' => empty($id)]
        );
        $mform->addRule('userids', null, 'required', null, 'client');
        $mform->addHelpButton('userids', 'participant', 'availability_maxviews');
        if ($id) {
            $mform->freeze('userids');
        }
        if ($type === 'normal' || !empty($id)) { // Normal override or editing.
            $identifier = 'maxviews';
        } else { // Additional maxviews.
            $identifier = 'maxviews_add';
        }
        $mform->addElement('text', 'maxviews', get_string($identifier, 'availability_maxviews'));
        $mform->setType('maxviews', PARAM_INT);
        $mform->addHelpButton('maxviews', $identifier, 'availability_maxviews');

        $mform->addElement('checkbox', 'resetviews', get_string('resetviews', 'availability_maxviews'));
        $mform->setType('resetviews', PARAM_INT);

        $this->add_action_buttons();
    }
}
