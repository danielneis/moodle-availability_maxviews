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
 * Upgrade this availability condition.
 * @param int $oldversion The old version of the component.
 * @return bool
 */
function xmldb_availability_maxviews_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023041201) {

        // Define table availability_maxviews to be created.
        $table = new xmldb_table('availability_maxviews');

        // Adding fields to table availability_maxviews.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastreset', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('maxviews', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table availability_maxviews.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('cmid', XMLDB_KEY_FOREIGN, ['cmid'], 'course_modules', ['id']);

        // Adding indexes to table availability_maxviews.
        $table->add_index('lastreset', XMLDB_INDEX_NOTUNIQUE, ['lastreset']);

        // Conditionally launch create table for availability_maxviews.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Maxviews savepoint reached.
        upgrade_plugin_savepoint(true, 2023041201, 'availability', 'maxviews');
    }

    if ($oldversion < 2023051717) {
        // Define table availability_maxviews already created.
        $table = new xmldb_table('availability_maxviews');
        // Define the new field for overrider id.
        $field = new xmldb_field('overriderid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0, 'userid');
        $field->setComment('The id of the user who did the overriding process.');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define the new field for timecreated.
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0, 'maxviews');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define the new field for timemodified.
        $field = new xmldb_field('timeupdated', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0, 'timecreated');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Maxviews savepoint reached.
        upgrade_plugin_savepoint(true, 2023051717, 'availability', 'maxviews');
    }

    return true;
}
