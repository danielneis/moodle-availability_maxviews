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
 * Language strings.
 *
 * @package availability_maxviews
 * @copyright 2015 Daniel Neis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addition_override'] = 'Adding maxviews';
$string['ajaxerror'] = 'Error contacting server';
$string['confirmdeleteoverride'] = 'Are you sure you want to delete the override for {$a}? This action cannot be undone.';
$string['coursemodule'] = 'Course module';
$string['coursemodule_help'] = 'select the course module needed to override it.';
$string['description'] = 'Prevent access after user view the module a certain number of times.';
$string['editingoverride'] = 'Editing override';
$string['eithernotdescription'] = 'you have not reached the limit of {$a->viewslimit} views (you have {$a->viewscount} views)';
$string['eitherdescription'] = 'you have reached the limit of {$a->viewslimit} views (you have {$a->viewscount} views)';
$string['event_maxviews_override_created'] = 'Limitation of maxviews override created';
$string['event_maxviews_override_description'] = 'The maxviews of user with id {$a->relateduserid} in course module with id {$a->cmid}
override {$a->type} by user of id {$a->userid} and {$a->reset}';
$string['event_maxviews_override_updated'] = 'Limitation of maxviews override updated';
$string['event_maxviews_override_deleted'] = 'Limitation of maxviews override deleted';
$string['fieldlabel'] = 'Maximum views:';
$string['lastreset'] = 'Last reset';
$string['maxviews'] = 'Maximum views';
$string['maxviews_add'] = 'Additional Maximum views';
$string['maxviews_help'] = 'New maximum views to override the main maxviews of the course module for the selected user.';
$string['maxviews_add_help'] = 'Additional maximum views added to the main maxviews of the course module for the selected user.';
$string['maxviews:override'] = 'Ability to override maxviews';
$string['newoverride'] = 'New override';
$string['normal_override'] = 'Normal override';
$string['overrider'] = 'Overrided by';
$string['nooverrides'] = 'No overrides';
$string['overrideadded'] = 'Override added';
$string['overridedeleted'] = 'Override deleted';
$string['overridetime'] = 'Overrided at';
$string['overridetype'] = 'Type of override';
$string['overridetype_desc'] = 'Normal override: discarding the old condition and set a new one for the selected users.

Adding maxviews: This mean that you add or subtract the new value from the old maxviews.';
$string['overrides'] = 'Overrides';
$string['overrideupdated'] = 'Override updated';
$string['participant'] = 'Participant';
$string['participant_help'] = 'Select the participants you want to override the maximum views for the selected course module.';
$string['pluginname'] = 'Max Views';
$string['resetviews'] = 'Reset views';
$string['resetviews_help'] = 'If this options checked, the views count for the selected users will be reset to zero for the selected course module.';
$string['resetyes'] = 'maxviews has been reset';
$string['resetno'] = 'maxviews not reset';
$string['settingpage'] = 'Settings for availability condition maxviews';
$string['title'] = 'Maximum Views';
$string['validnumber'] = 'You must add a number higher than 0';
