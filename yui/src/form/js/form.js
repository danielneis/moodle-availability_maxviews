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
 * Because the date fields are complex depending on Moodle calendar settings,
 * we create the HTML for these fields in PHP and pass it to this method.
 *
 * @method initInner
 * @param {String} html HTML to use for date fields
 * @param {Number} defaultTime Time value that corresponds to initial fields
 */
M.availability_maxviews.form.initInner = function(contextid, viewslimit) {
    this.contextid = contextid;
    this.viewslimit = viewslimit;
    this.label = 'Maximum views is less than:';
};

M.availability_maxviews.form.getNode = function(json) {

    // Example controls contain only one tickbox.
    var html = '<label>' + this.label + ' <input type="number" /></label>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values based on the value from the JSON data in Moodle
    // database. This will have values undefined if creating a new one.
    if (json.viewslimit) {
        node.one('input').set('value', json.viewslimit);
    }

    // Add event handlers (first time only). You can do this any way you
    // like, but this pattern is used by the existing code.
    if (!M.availability_maxviews.form.addedEvents) {
        M.availability_maxviews.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('click', function() {
                // The key point is this update call. This call will update
                // the JSON data in the hidden field in the form, so that it
                // includes the new value of the checkbox.
                M.core_availability.form.update();
                }, '.availability_maxviews input');
    }

    return node;
};

M.availability_maxviews.form.fillValue = function(value, node) {
    // This function gets passed the node (from above) and a value
    // object. Within that object, it must set up the correct values
    // to use within the JSON data in the form. Should be compatible
    // with the structure used in the __construct and save functions
    // within condition.php.
    var viewslimitinput = node.one('input');
    value.viewslimit = viewslimitinput.get('value');
};

M.availability_completion.form.fillErrors = function(errors, node) {
    // If the user has selected something invalid, this optional
    // function can be included to report an error in the form. The
    // error will show immediately as a 'Please set' tag, and if the
    // user saves the form with an error still in place, they'll see
    // the actual error text.
 
    // In this example an error is not possible...
    if (false) {
        // ...but this is how you would add one if required. This is
        // passing your component name (availability_maxviews) and the
        // name of a string within your lang file (error_message)
        // which will be shown if they submit the form.
        node.one('input');
        errors.push('availability_maxviews:error_message');
    }
};
