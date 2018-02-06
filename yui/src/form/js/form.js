/**
 * JavaScript for form editing date conditions.
 *
 * @module moodle-availability_maxviews-form
 */
M.availability_maxviews = M.availability_maxviews || {};

/**
 * @class M.availability_maxviews.form
 * @extends M.core_availability.plugin
 */
M.availability_maxviews.form = Y.Object(M.core_availability.plugin);

/**
 * Initialises this plugin.
 *
 * @method initInner
 */
M.availability_maxviews.form.initInner = function() {
    // Does nothing.
};

M.availability_maxviews.form.instId = 1;

M.availability_maxviews.form.getNode = function(json) {
    "use strict";
    var html, root, node, id;

    id = 'maxviews' + M.availability_maxviews.form.instId;
    M.availability_maxviews.form.instId += 1;

    // Create HTML structure.
    html = '';
    html += '<label for="' + id + '">';
    html += M.util.get_string('fieldlabel', 'availability_maxviews') + ' </label>';
    html += ' <input type="number" name="maxviews" id="' + id + '">';
    node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values based on the value from the JSON data in Moodle
    // database. This will have values undefined if creating a new one.
    if (json.viewslimit !== undefined) {
        node.one('input[name=maxviews]').set('value', json.viewslimit);
    }

    // Add event handlers (first time only). You can do this any way you
    // like, but this pattern is used by the existing code.
    if (!M.availability_maxviews.form.addedEvents) {
        M.availability_maxviews.form.addedEvents = true;
        root = Y.one('.availability-field');
        root.delegate('change', function() {

            M.core_availability.form.update();
        }, '.availability_maxviews input[name=maxviews]');
    }

    return node;
};

M.availability_maxviews.form.fillValue = function(value, node) {
    "use strict";

    value.viewslimit = node.one('input[name=maxviews]').get('value');
};


M.availability_maxviews.form.fillErrors = function(errors, node) {
    "use strict";
    var value = {};
    this.fillValue(value, node);

    // Check the password has been set.
    if (value.viewslimit === undefined || value.viewslimit === '' || value.viewslimit <= 0) {
        errors.push('availability_maxviews:validnumber');
    }
};
