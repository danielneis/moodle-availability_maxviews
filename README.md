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

When you are adding any content to a course, you'll be able to add a "Max views" condition.
For each of these conditions you can set a max number of views that will be accounted for each user.
After the user access the content that many times, he/she will not be able to access it anymore.

Dev Info
--------

Please, report issues at: https://github.com/danielneis/moodle-availability_maxviews/issues

Feel free to send pull requests at: https://github.com/danielneis/moodle-availability_maxviews/pulls

[![Build Status](https://travis-ci.org/danielneis/moodle-availability_maxviews.svg)](https://travis-ci.org/danielneis/moodle-availability_maxviews)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/danielneis/moodle-availability_maxviews/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/danielneis/moodle-availability_maxviews/?branch=master)
