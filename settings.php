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
 * Setting page for availability maxviews
 *
 * @package    availability_maxviews.
 * @copyright  Mo Farouk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('availability_maxviews/settings',
                                                get_string('settingpage', 'availability_maxviews'), ''));

    $options = [
        'normal' => get_string('normal_override', 'availability_maxviews'),
        'add'    => get_string('addition_override', 'availability_maxviews'),
    ];
    $settings->add(new admin_setting_configselect('availability_maxviews/overridetype',
                                                get_string('overridetype', 'availability_maxviews'),
                                                get_string('overridetype_desc', 'availability_maxviews'),
                                                'normal',
                                                $options));

    $options = [
        'all'    => get_string('allenroled', 'availability_maxviews'),
        'filter' => get_string('filterenroled', 'availability_maxviews'),
    ];
    $settings->add(new admin_setting_configselect('availability_maxviews/filterusers',
                                                get_string('filterusers', 'availability_maxviews'),
                                                get_string('filterusers_desc', 'availability_maxviews'),
                                                'all',
                                                $options));
}

