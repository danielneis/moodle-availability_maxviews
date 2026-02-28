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

namespace availability_maxviews\local;

/**
 * Class info_module
 *
 * @package    availability_maxviews
 * @copyright  2026 Mohammad Farouk <phun.for.physics@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class info_module extends \core_availability\info_module {
    /**
     * Override to avoid throwing exceptions by calling it outside
     * checking availability in the info class itself.
     * @return \course_modinfo|null
     */
    public function get_modinfo() {
        if (isset($this->modinfo)) {
            return $this->modinfo;
        }
        return get_fast_modinfo($this->get_course());
    }
}
