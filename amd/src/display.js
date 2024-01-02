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
 * Display the views limits for course modules.
 *
 * @module     availability_maxviews/display
 * @copyright  2024 Mohammad Farouk <phun.for.physics@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from "jquery";
// import ajax from "core/ajax";

export const init = (cmid, render) => {
    // The following listener is needed for the Tiles course format, where sections are loaded on demand.
    // $(document).ajaxComplete(function(event, xhr, settings) {
    //     if (typeof (settings.data) !== 'undefined') {
    //         var data = JSON.parse(settings.data);
    //         if (data.length > 0 && typeof (data[0].methodname) !== 'undefined') {
    //             if (data[0].methodname == 'format_tiles_get_single_section_page_html' // Tile load.
    //                 || data[0].methodname == 'format_tiles_log_tile_click'
    //                 ) { // Tile load, cached.
    //                 $(document).ready(function() {
    //                     var existed = $("#availability_maxviews_count_" + cmid);
    //                     if (!existed || !existed[0] || existed[0] == undefined) {
    //                         var module = $("#module-"+cmid+" .description");
    //                         module.append(render);
    //                     }
    //                 });
    //             }
    //         }
    //     }
    // });
    // For any other course format.
    $(document).ready(function() {
        setInterval(function() {
            var existed = $("#availability_maxviews_count_"+cmid);
            if (!existed || !existed[0] || existed[0] == undefined) {
                var module = $("#module-"+cmid+" .description");
                if (module !== null) {
                    module.append(render);
                }
            }
        }, 500);
    });
};
