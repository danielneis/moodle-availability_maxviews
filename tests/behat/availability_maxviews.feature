@availability @availability_maxviews
Feature: availability_maxviews
  In order to control student access to activities
  As a teacher
  I need to set date conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | enableavailability  | 1 |

  @javascript
  Scenario: Max views must work with Page activity
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Page with 0 max view allowed.
    And I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name         | Page 1 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a Page with 1 max view allowed.
    And I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name         | Page 2 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "1"
    And I press "Save and return to course"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    # Page 1 should not appear, but page 2 does.
    Then I should not see "Page 1" in the "region-main" "region"
    Then I should see "Page 2" in the "region-main" "region"

    When I follow "Page 2"
    And I follow "Course 1"

    # Page 2 should not appear anymore.
    Then I should not see "Page 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with Assignment activity
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Page with 0 max view allowed.
    And I add a "Assignment" to section "1"
    And I set the following fields to these values:
      | Name         | Assignment 1 |
      | Description  | Test   |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a Page with 1 max view allowed.
    And I add a "Assignment" to section "1"
    And I set the following fields to these values:
      | Name         | Assignment 2 |
      | Description  | Test   |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "1"
    And I press "Save and return to course"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    # Page 1 should not appear, but page 2 does.
    Then I should not see "Assignment 1" in the "region-main" "region"
    Then I should see "Assignment 2" in the "region-main" "region"

    When I follow "Assignment 2"
    And I follow "Course 1"

    # Page 2 should not appear anymore.
    Then I should not see "Assignment 2" in the "region-main" "region"
