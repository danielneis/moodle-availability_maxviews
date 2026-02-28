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

const config = {attributes: false, childList: true, subtree: true};

/**
 *
 * @param {number} cmid the cm id
 * @param {string} render the content
 */
export const init = (cmid, render) => {
    // For any course format.
    // Some course formats like Tiles uses Ajax to display course modules.
    // Use mutation observers to observe any change in the main region
    // to display the required information.

    // Create an observer instance linked to the callback function
    const observer = new MutationObserver(callback);

    $(function() {
        let regionMain = document.getElementById("region-main");
        appendViews(cmid, render);
        // Start observing the target node for configured mutations
        observer.observe(regionMain, config);
    });

    /**
     * Callback when a mutation happen.
     *
     * @param {*} mutationList
     */
    function callback(mutationList) {
        for (const mutation of mutationList) {
            if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
                appendViews(cmid, render);
            }
        }
    }
};

/**
 * Append information about limitation of max number allowed to view a course module.
 * @param {number} cmid
 * @param {string} render
 */
function appendViews(cmid, render) {
    let existed = $("#availability_maxviews_count_" + cmid);
    if (!existed || !existed[0] || existed[0] == undefined) {
        let module = $("#module-" + cmid + ' .activity-item');
        if (module !== null) {
            module.append(render);
        }
    }
}
