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

namespace availability_maxviews;

use availability_maxviews\local\display;
use core\hook\output\before_footer_html_generation;

/**
 * Class callbacks
 *
 * @package    availability_maxviews
 * @copyright  2024 Mohammad Farouk <phun.for.physics@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class callbacks {
    /**
     * Inject js to display maxviews limits in each activity.
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     * @return void
     */
    public static function inject_js(before_footer_html_generation $hook) {
        global $DB;

        if (during_initial_install()) {
            return;
        }

        if (!class_exists('availability_maxviews\local\display')) {
            return;
        }

        display::display($hook->renderer);
    }
}
