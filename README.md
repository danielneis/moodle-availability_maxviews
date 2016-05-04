Maximum views Availability Condition for Moodle
-----------------------------------------------

An availability condition for Moodle that limits the number of views of an activity by users

Install
-------

* Put these files at moodle/availability/condition/maxviews/
 * You may use composer
 * or git clone
 * or download the latest version from https://github.com/danielneis/moodle-availability_maxviews/archive/master.zip
* Log in your Moodle as Admin and go to "Notifications" page
* Follow the instructions to install the plugin
* Disable Legacy logs for this plugin to work correctly.

Usage
-----

* Go to a course, turn edit on, choose a module to add or edit
* In the 'Availability restrictions' section you'll be able to add a "Max views" condition.
* This type of condition allows you to set a maximum number of views that will be accounted for each user and after the user access the content that many times, he/she will not be able to access it anymore.

This block is tested with the following plugins and is working:

* Assignment
* File
* Forum
* Lesson
* Page
* URL

This block is also tested with the following plugins that are not working:

* Book
* Label
* Wiki

If you are interested in using this plugin with the ones that are not working please take a look at #2.

Dev Info
--------

Please, report issues at: https://github.com/danielneis/moodle-availability_maxviews/issues

Feel free to send pull requests at: https://github.com/danielneis/moodle-availability_maxviews/pulls

[![Build Status](https://travis-ci.org/danielneis/moodle-availability_maxviews.svg)](https://travis-ci.org/danielneis/moodle-availability_maxviews)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/danielneis/moodle-availability_maxviews/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/danielneis/moodle-availability_maxviews/?branch=master)
