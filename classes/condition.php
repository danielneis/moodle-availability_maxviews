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
 * Date condition.
 *
 * @package availability_maxviews
 * @copyright 2015 Daniel Neis Araujo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_maxviews;

use availability_maxviews\local\display;
use availability_maxviews\local\utils;
use core_availability\info;
use core_availability\info_module;
use core_availability\info_section;

/**
 * maxviews condition.
 *
 * @package availability_maxviews
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    /**
     * The view limit.
     * @var int
     */
    public readonly int $viewslimit;

    /**
     * Only used for display maxviews notice and should not impact restriction.
     * @var int
     */
    public readonly int $forceavailable;
    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        $this->viewslimit = $structure->viewslimit;
        $this->forceavailable = $structure->forceavailable ?? 0;
    }

    /**
     * Create object to be saved representing this condition.
     */
    public function save() {
        return (object)array('type' => 'maxviews', 'viewslimit' => $this->viewslimit);
    }

    /**
     * Returns a JSON object which corresponds to a condition of this type.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * @param int $viewslimit The limit of views for users
     * @return \stdClass Object representing condition
     */
    public static function get_json($viewslimit = 5) {
        return (object)array('type' => 'maxviews', 'viewslimit' => (int)$viewslimit);
    }

    /**
     * Determines whether a particular item is currently available
     * according to this availability condition.
     *
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid User ID to check availability for
     * @return bool True if available
     */
    public function is_available($not, info $info, $grabthelot, $userid) {

        if ($this->forceavailable) {
            $allow = $this->forceavailable === display::FORCE_AV;
            return $not ? !$allow : $allow;
        }

        [$viewscount, $viewslimit] = $this->get_views_count_limit($info, $this->viewslimit, $userid);

        $allow = ($viewscount < $viewslimit);
        if ($not) {
            $allow = !$allow;
        }

        return $allow;
    }

    /**
     * Obtains a string describing this restriction (whether or not
     * it actually applies).
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_description($full, $not, info $info) {
        global $DB;

        [$viewscount, $viewslimit] = $this->get_views_count_limit($info, $this->viewslimit);

        $a = new \stdclass();
        $a->viewslimit = $viewslimit;
        $a->viewscount = $viewscount;

        if ($not) {
            return get_string('eithernotdescription', 'availability_maxviews', $a);
        } else {
            return get_string('eitherdescription', 'availability_maxviews', $a);
        }
    }

    /**
     * Return the count of views and the limits for a given user.
     * @param info $info
     * @param int $userid if null it will refer to the current user.
     * @return [] [viewscount, viewslimit]
     */
    public static function get_views_count_limit(info $info, int $viewslimit, ?int $userid = null): array {
        $context = $info->get_context();

        [$cmid, $kind] = self::get_selfid($info);
        return utils::get_views_count_limit($context->id, $viewslimit, $cmid, $kind, $userid);
    }

    /**
     * Return current item ID (cmid or sectionid).
     *
     * @param info $info
     * @return array cmid/sectionid/null
     */
    protected static function get_selfid(info $info): ?array {

        if ($info instanceof info_module) {
            $cminfo = $info->get_course_module();
            if (!empty($cminfo->id)) {
                 return [$cminfo->id, 'cm'];
            }
        }

        if ($info instanceof info_section) {
            $section = $info->get_section();
            if (!empty($section->id)) {
                return [$section->id, 'section'];
            }
        }

        return [null, ''];
    }

    /**
     * Obtains a representation of the options of this condition as a string,
     * for debugging.
     *
     * @return string Text representation of parameters
     */
    protected function get_debug_string() {
        return gmdate('Y-m-d H:i:s');
    }
}
