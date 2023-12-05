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
 * Plugin lib.
 *
 * @package availability_maxviews
 * @copyright 2023 Daniel Neis Araujo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function availability_maxviews_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    global $PAGE;

    if (has_capability('availability/maxviews:override', $context)) {
                // Find the course settings node using the 'courseadmin' key.

        $url = new moodle_url('/availability/condition/maxviews/index.php', ['courseid' => $course->id]);
        $pluginname = get_string('pluginname', 'availability_maxviews');
        $node = navigation_node::create(
            $pluginname,
            $url,
            navigation_node::NODETYPE_LEAF,
            null,
            'maxviews',
            new pix_icon('i/report', $pluginname, 'availability_maxviews')
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $node->make_active();
        }
        $navigation->add_node($node);
    }
}

/**
 * Callback to display the views limits for course modules.
 */
function availability_maxviews_before_footer() {
    global $USER, $COURSE, $DB, $PAGE, $OUTPUT;
    $modinfo = get_fast_modinfo($COURSE->id);
    $cms = $modinfo->cms;
    $moduleswithmaxviews = [];
    foreach ($cms as $cm) {
        $getviewslimit = $DB->get_field_select('course_modules', 'availability' ,
        "`availability` LIKE '%maxviews%' AND `id` = '$cm->id'", ['id' => $cm->id], IGNORE_MISSING);
        if ($getviewslimit != null && $cm->uservisible) {
            $moduleswithmaxviews[] = $cm->id;
        }
    }

    if (empty($moduleswithmaxviews)) {
        return;
    }

    $cms = $moduleswithmaxviews;
    // Get the views of current user and get string from availability max-views.
    $logmanager = get_log_manager();
    if (!$readers = $logmanager->get_readers('core\log\sql_reader')) {
        // Should be using 2.8, use old class.
        $readers = $logmanager->get_readers('core\log\sql_select_reader');
    }
    $reader = array_pop($readers);

    // This loop for each course module in the current course.
    foreach ($cms as $cmid) {
        $cm = $modinfo->get_cm($cmid);
        $info = new core_availability\info_module($cm);
        $context = $info->get_context();

        // Get the views limits for this module from availability_maxviews.
        $tree = $info->get_availability_tree();
        $reflectiontree = new \ReflectionClass($tree);
        $reflectionchildren = $reflectiontree->getProperty('children');
        $reflectionchildren->setAccessible(true);

        $children = $reflectionchildren->getValue($tree);

        $viewslimit = PHP_INT_MAX;

        foreach ($children as $child) {
            if ($child instanceof \availability_maxviews\condition) {
                // This is a max views condition.

                // Viewslimit is protected so again use reflection.
                $reflectionmaxviews = new \ReflectionClass($child);
                $reflectionviewslimit = $reflectionmaxviews->getProperty('viewslimit');
                $reflectionviewslimit->setAccessible(true);

                $newviewslimit = (int)$reflectionviewslimit->getValue($child);

                // It's unlikely but its possible to add more than one max views condition.
                // So use the smallest value.
                if ($newviewslimit < $viewslimit) {
                    $viewslimit = $newviewslimit;
                }

            }
        }
        $where = 'contextid = :context AND userid = :userid AND crud = :crud';
        $params = ['context' => $context->id, 'userid' => $USER->id, 'crud' => 'r'];
        $conditions = ['cmid' => $info->get_course_module()->id, 'userid' => $USER->id];
        if ($override = $DB->get_record('availability_maxviews', $conditions)) {
            // To make sure that it is numeric integer value.
            $lastreset = intval($override->lastreset);
            if (!empty($lastreset)) {
                $where .= ' AND timecreated >= :lastreset';
                $params['lastreset'] = $override->lastreset;
            }
            // Check the type of overriding.
            $type = get_config('availability_maxviews', 'overridetype');
            // If there is override, set the new value according to the type of override.
            if (!empty($override->maxviews)) {
                if ($type == 'normal') {
                    $viewslimit = $override->maxviews;
                } else if ($type == 'add') {
                    $viewslimit += $override->maxviews;
                }
            }
        }
        $viewscount = $reader->get_events_select_count($where, $params);

        // Prepare for the output.
        $a = new \stdclass();
        $a->viewscount = $viewscount;
        $a->viewslimit = $viewslimit;
        $a->viewsremain = ($viewslimit - $viewscount);

        if ($a->viewslimit > $a->viewscount) {
            $note = get_string('haveremainedviews', 'availability_maxviews', $a);
            $type = 'warning';
        } else {
            $note = get_string('outofviews', 'availability_maxviews', $a);
            $type = 'error';
        }

        $render = html_writer::start_div('', ['id' => "availability_maxviews_count_$cmid"]);
        $render .= $OUTPUT->notification($note, $type);
        $render .= html_writer::end_div();

        // JavaScript code to show the views of certain user even if he didn't restricted.
        // For Tiles course format.
        $code = '
            require(["jquery", "core/ajax"], function($, ajax) {
                // The following listener is needed for the Tiles course format, where sections are loaded on demand.
                $(document).ajaxComplete(function(event, xhr, settings) {
                    if (typeof (settings.data) !== \'undefined\') {
                        var data = JSON.parse(settings.data);
                        if (data.length > 0 && typeof (data[0].methodname) !== \'undefined\') {
                            if (data[0].methodname == \'format_tiles_get_single_section_page_html\' // Tile load.
                                // || data[0].methodname == \'format_tiles_log_tile_click\'
                                ) { // Tile load, cached.
                                $(document).ready(function() {
                                    var existed = $("#availability_maxviews_count_'.$cmid.'");
                                    if (existed) {
                                        // Remove exist element.
                                        existed.remove();
                                    }
                                    var module = $("#module-'.$cmid.' .description");
                                    module.append("'.addslashes_js($render).'");
                                });
                            }
                        }
                    }
                });
                $(document).ready(function() {
                    var x = setInterval(function() {
                        var existed = $("#availability_maxviews_count_'.$cmid.'");
                        if (existed) {
                            // Remove exist element.
                            existed.remove();
                        }
                        var module = $("#module-'.$cmid.' .description");
                        if (module != null) {
                            module.append("'.addslashes_js($render).'");
                            if ($("#availability_maxviews_count_'.$cmid.'")) {
                                clearInterval(x);
                            }
                        }
                    });
                });
            });
        ';

        $PAGE->requires->js_init_code($code);
    }
}
