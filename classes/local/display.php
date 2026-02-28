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

use cm_info;
use core\output\notification;
use core_availability\tree;
use html_writer;
use renderer_base;
use stdClass;

/**
 * Class display.
 *
 * @package    availability_maxviews
 * @copyright  2026 Mohammad Farouk <phun.for.physics@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class display {
    /**
     * Force maxviews to be available.
     * @var int
     */
    public const FORCE_AV = 1;

    /**
     * Force maxviews to be unavailable.
     * @var int
     */
    public const FORCE_NOT_AV = 2;

    /**
     * Availability info for the course module.
     * @var info_module
     */
    public readonly info_module $info;

    /**
     * Summary of __construct.
     * @param cm_info $cm
     */
    public function __construct(
        /**
         * @var cm_info Course module.
         */
        public readonly cm_info $cm
    ) {
    }

    /**
     * Return the current availability info module.
     * @return info_module
     */
    protected function get_info_module(): info_module {
        if (isset($this->info)) {
            return $this->info;
        }
        $this->info = new info_module($this->cm);

        return $this->info;
    }

    /**
     * Check if the course module is visible or not.
     * @return bool
     */
    public function is_visible(): bool {
        $cm = $this->cm;

        return !$cm->is_user_access_restricted_by_capability() && !$cm->is_stealth() && $cm->is_visible_on_course_page() && $cm->get_user_visible();
    }

    /**
     * Get the availability condition class.
     * @param  stdClass                      $condition
     * @return ?\core_availability\condition
     */
    protected function get_av_condition_class(stdClass $condition): ?\core_availability\condition {
        static $enabled;
        $classname = "\availability_{$condition->type}\condition";

        if (!class_exists($classname)) {
            return null;
        }

        if (!isset($enabled)) {
            $pluginmanager = \core_plugin_manager::instance();
            $enabled       = $pluginmanager->get_enabled_plugins('availability');
        }

        if (!\in_array($condition->type, $enabled)) {
            return null;
        }

        return new $classname($condition);
    }

    /**
     * Mark all maxview instances as visible just to check if it is reachable by other restrictions.
     * @param  stdClass $s
     * @return void
     */
    protected function mark_all_max_views_as_available(stdClass &$s, int &$count): void {
        $andoperator = match($s->op) {
            tree::OP_AND, tree::OP_NOT_AND => true,
            tree::OP_NOT_OR, tree::OP_OR   => false,
        };

        foreach ($s->c as &$restriction) {
            // Checking for restrictions of kind max-views.
            if (!empty($restriction->type)) {
                if ($restriction->type == 'maxviews') {
                    $count++;
                    $restriction->forceavailable = $andoperator ? self::FORCE_AV : self::FORCE_NOT_AV;
                }
            } else if (!empty($restriction->c)) {
                $this->mark_all_max_views_as_available($restriction, $count);
            }
        }
    }

    /**
     * Check if availability condition plugin is available for the current user.
     * @param  \core_availability\condition $class
     * @param  bool                         $innernot
     * @return bool
     */
    protected function is_condition_available(\core_availability\condition $class, bool $innernot): bool {
        global $USER;

        return $class->is_available($innernot, $this->get_info_module(), true, $USER->id);
    }

    /**
     * Check if the tree is available for the current user.
     * @param  stdClass $condition
     * @param  bool     $innernot
     * @return bool
     */
    protected function is_tree_available(stdClass $condition, bool $innernot = false): bool {
        global $USER;
        $root = !empty($condition->showc);
        $tree = new tree($condition, true, $root);
        $info = $this->get_info_module();
        $info->get_modinfo();

        return $tree->check_available($innernot, $info, true, $USER->id)->is_available();
    }

    /**
     * Get information about maxviews availability.
     * @param  stdClass                                                                    $s
     * @return array[]|array{and: bool, av: bool, inner: array, maxviews: ?int, not: bool}
     */
    protected function get_maxviews_info(stdClass $s) {
        $count = 0;
        static::mark_all_max_views_as_available($s, $count);

        $root                     = !empty($s->showc);
        $tree                     = new tree($s, true, $root);
        [$innernot, $andoperator] = $tree->get_logic_flags(false);
        $av                       = ($count === \count($s->c)) || $this->is_tree_available($s, $innernot);

        $mtree = [
            'and'       => $andoperator,
            'not'       => $innernot,
            'av'        => $av,
            'maxviews'  => [],
            'inner'     => [],
        ];

        foreach ($s->c as $restriction) {
            // Checking for restrictions of kind max-views.
            if (!empty($restriction->type)) {
                if ($restriction->type == 'maxviews') {
                    $mtree['maxviews'][] = (int)$restriction->viewslimit;
                } else {
                    $class = $this->get_av_condition_class($restriction);

                    if (!$class) {
                        continue;
                    }
                    $av = match($andoperator) {
                        true  => $av || $this->is_condition_available($class, $innernot),
                        false => $av && $this->is_condition_available($class, $innernot),
                    };
                }
            } else if (!empty($restriction->c)) {
                $mtree['inner'] = $this->get_maxviews_info($restriction);
                $av             = match($andoperator) {
                    true  => $av || $this->is_tree_available($restriction, $innernot),
                    false => $av && $this->is_tree_available($restriction, $innernot),
                };
            }
        }

        $mtree['av']       = $av;
        $mtree['maxviews'] = (\count($mtree['maxviews']) > 0)
                           ? ($andoperator ? min($mtree['maxviews']) : max($mtree['maxviews']))
                           : null;

        return $mtree;
    }

    /**
     * Extract the maxviews numbers available conditions and sets.
     * @param  array $info
     * @param  array $maxviews
     * @return void
     */
    protected function extract_max_views(array $info, ?int &$maxviews = null, ?bool $outerand = null): void {
        if (empty($info['inner']) && ((!$info['av'] && $info['and']) || ($info['av'] && !$info['and']))) {
            return;
        }

        if (!empty($info['maxviews'])) {
            if ($maxviews === null) {
                $maxviews = $info['maxviews'];
            } else if ($outerand !== null) {
                $maxviews = match($outerand) {
                    true  => min($maxviews, $info['maxviews']),
                    false => max($maxviews, $info['maxviews']),
                };
            }
        }

        if (!empty($info['inner'])) {
            $outerand = ($outerand !== null) ? $outerand && $info['and'] : $info['and'];

            if ($info['not']) {
                $outerand = !$outerand;
            }
            $this->extract_max_views($info['inner'], $maxviews, $outerand);
        }
    }

    /**
     * Extract all limits by maxview availability in a given availability tree.
     * @return int
     */
    public function get_maxviews(): ?int {
        $cm   = $this->cm;
        $tree = @json_decode($cm->availability);

        if (!$tree || empty($tree->c) || !$this->is_visible()) {
            return null;
        }

        $info = self::get_maxviews_info($tree);
        $this->extract_max_views($info, $maxviews);

        return $maxviews;
    }

    public static function display(renderer_base $output) {
        if (empty(get_config('availability_maxviews', 'showlimits'))) {
            return;
        }

        $page   = $output->get_page();
        $course = $page->course;

        if (empty($course->id) || $course->id == SITEID || !$page->has_set_url()) {
            return;
        }

        $urlpath = $page->url->get_path();

        if (!stristr($urlpath, 'course/view.php')) {
            return;
        }

        $modinfo = get_fast_modinfo($course->id);
        $cms     = $modinfo->cms;

        foreach ($cms as $key => $cm) {
            $cmid = $cm->id;

            if (empty($cm->availability)) {
                continue;
            }

            $display = new static($cm);

            if (!$display->is_visible()) {
                echo 'Not visible';

                return;
            }

            $viewslimit = $display->get_maxviews();

            if (empty($viewslimit)) {
                // Not a max-views.
                unset($cms[$key]);
                continue;
            }

            // Get the views count and add check overridden maxviews limits.
            [$viewscount, $viewslimit] = utils::get_views_count_limit($cm->context->id, $viewslimit, $cm->id);

            // Prepare for the output.
            $a              = new stdclass();
            $a->viewscount  = $viewscount;
            $a->viewslimit  = $viewslimit;
            $a->viewsremain = ($viewslimit - $viewscount);

            if ($a->viewslimit > $a->viewscount) {
                $note = get_string('haveremainedviews', 'availability_maxviews', $a);
                $type = 'warning';
            } else {
                $note = get_string('outofviews', 'availability_maxviews', $a);
                $type = 'error';
            }

            $render = html_writer::start_div('', ['id' => "availability_maxviews_count_$cmid"]);

            if (!method_exists($output, 'notification')) {
                $notify = new notification($note, $type);
                $render .= $output->render_from_template($notify->get_template_name(), $notify->export_for_template($output));
            } else {
                $render .= $output->notification($note, $type);
            }

            $render .= html_writer::end_div();

            $page->requires->js_call_amd('availability_maxviews/display', 'init', ['cmid' => $cmid, 'render' => $render]);
        }
    }
}
