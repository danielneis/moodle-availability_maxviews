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
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $modinfo = get_fast_modinfo($this->_customdata['courseid']);
        $options = ['' => ''];
        foreach ($modinfo->cms as $i) {
            $options[$i->id] = $i->name;
        }
        $autocomplete = $mform->addElement(
            'autocomplete',
            'cmid',
            get_string('coursemodule', 'availability_maxviews'),
            $options,
            [],
        );
        $mform->addRule('cmid', null, 'required', null, 'client');

        $options = ['' => ''];
        $users = get_enrolled_users(\context_course::instance($this->_customdata['courseid']));
        foreach ($users as $u) {
            $options[$u->id] = fullname($u);
        }
        $autocomplete = $mform->addElement(
            'autocomplete',
            'userid',
            get_string('participant', 'availability_maxviews'),
            $options,
        );
        $mform->addRule('userid', null, 'required', null, 'client');

        $mform->addElement('text', 'maxviews', get_string('maxviews', 'availability_maxviews'));
        $mform->setType('maxviews', PARAM_TEXT);

        $mform->addElement('checkbox', 'resetviews', get_string('resetviews', 'availability_maxviews'));
        $mform->setType('resetviews', PARAM_INT);

        $this->add_action_buttons();
    }
}
