@availability @availability_maxviews
Feature: availability_maxviews
  In order to control student access to activities
  As a teacher
  I need to set maximum number of views which prevent student access

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
  Scenario: Max views must work with Assignment activity
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Assignment with 0 max view allowed.
    And I add a "Assignment" to section "1"
    And I set the following fields to these values:
      | Assignment name | Assignment 1 |
      | Description  | Test assignment description 1 |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a Assignment with 1 max view allowed.
    And I add a "Assignment" to section "1"
    And I set the following fields to these values:
      | Assignment name | Assignment 2 |
      | Description  | Test assignment description 2 |
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

    Then I should not see "Assignment 1" in the "region-main" "region"
    Then I should see "Assignment 2" in the "region-main" "region"

    When I follow "Assignment 2"
    And I follow "Course 1"

    Then I should not see "Assignment 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with Book resources
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Book with 0 max view allowed
    When I add a "Book" to section "1"
    And I set the following fields to these values:
      | Name | Book 1 |
      | Description | The Book 1 |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"
    And I follow "Book 1"
    And I set the following fields to these values:
      | Chapter title | First chapter |
      | Content | First chapter |
    And I press "Save changes"
    And I follow "Course 1"

    # Add a Book with 1 max view allowed
    When I add a "Book" to section "1"
    And I set the following fields to these values:
      | Name | Book 2 |
      | Description | The Book 2 |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"
    And I follow "Book 2"
    And I set the following fields to these values:
      | Chapter title | First chapter |
      | Content | First chapter |
    And I press "Save changes"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    Then I should not see "Book 1" in the "region-main" "region"
    And I should see "Book 2" in the "region-main" "region"

    When I follow "Book 2"
    And I follow "Course 1"

    Then I should not see "Book 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with File resources
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a File with 0 max view allowed.
    When I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                      | File 1     |
      | Show size                 | 0          |
      | Show type                 | 0          |
      | Show upload/modified date | 0          |
    And I upload "availability/condition/maxviews/tests/fixtures/samplefile.txt" file to "Select files" filemanager
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a File with 1 max view allowed.
    When I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                      | File 2     |
      | Show size                 | 0          |
      | Show type                 | 0          |
      | Show upload/modified date | 0          |
    And I upload "availability/condition/maxviews/tests/fixtures/samplefile.txt" file to "Select files" filemanager
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

    Then I should not see "File 1" in the "region-main" "region"
    And I should see "File 2" in the "region-main" "region"

    When I follow "File 2"
    And I follow "Course 1"

    Then I should not see "File 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with Forum activity
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Forum with 0 max view allowed.
    And I add a "Forum" to section "1"
    And I set the following fields to these values:
      | Forum name | Forum 1 |
      | Description  | Test forum description 1 |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a Forum with 1 max view allowed.
    And I add a "Forum" to section "1"
    And I set the following fields to these values:
      | Forum name | Forum 2 |
      | Description  | Test forum description 2 |
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

    Then I should not see "Forum 1" in the "region-main" "region"
    Then I should see "Forum 2" in the "region-main" "region"

    When I follow "Forum 2"
    And I follow "Course 1"

    Then I should not see "Forum 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with Label activity
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Label with 0 max view allowed.
    And I add a "Label" to section "1"
    And I set the following fields to these values:
      | Label text | Label 1 |
      | Visible | Show |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a Label with 1 max view allowed.
    And I add a "Label" to section "1"
    And I set the following fields to these values:
      | Label text | Label 2 |
      | Visible | Show |
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

    Then I should not see "Label 1" in the "region-main" "region"
    Then I should see "Label 2" in the "region-main" "region"

    When I follow "Course 1"

    Then I should not see "Label 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with Lesson activity
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Lesson with 0 max view allowed.
    And I add a "Lesson" to section "1"
    And I set the following fields to these values:
      | Name | Lesson 1 |
      | Description  | Test lesson description 1 |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a Lesson with 1 max view allowed.
    And I add a "Lesson" to section "1"
    And I set the following fields to these values:
      | Name | Lesson 2 |
      | Description  | Test lesson description 2 |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "1"
    And I press "Save and return to course"
    And I follow "Lesson 2"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    Then I should not see "Lesson 1" in the "region-main" "region"
    And I should see "Lesson 2" in the "region-main" "region"

    When I follow "Lesson 2"
    And I follow "Course 1"

    Then I should not see "Lesson 2" in the "region-main" "region"

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

    Then I should not see "Page 1" in the "region-main" "region"
    Then I should see "Page 2" in the "region-main" "region"

    When I follow "Page 2"
    And I follow "Course 1"

    Then I should not see "Page 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with URL resources
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a URL with 0 max view allowed.
    And I add a "URL" to section "1"
    And I set the following fields to these values:
      | Name | URL 1 |
      | Description  | Test URL description 1 |
      | External URL | https://www.example.com |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a URL with 1 max view allowed.
    And I add a "URL" to section "1"
    And I set the following fields to these values:
      | Name | URL 2 |
      | Description  | Test URL description 2 |
      | External URL | https://www.secondexample.com |
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

    Then I should not see "URL 1" in the "region-main" "region"
    And I should see "URL 2" in the "region-main" "region"

    When I follow "URL 2"
    And I follow "Course 1"

    Then I should not see "URL 2" in the "region-main" "region"

  @javascript
  Scenario: Max views must work with Wiki activity
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Wiki with 0 max view allowed.
    And I add a "Wiki" to section "1"
    And I set the following fields to these values:
      | Wiki name | Wiki 1 |
      | Description  | Test wiki description 1 |
      | First page name | First page |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Maximum Views" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "maxviews" to "0"
    And I press "Save and return to course"

    # Add a Wiki with 1 max view allowed.
    And I add a "Wiki" to section "1"
    And I set the following fields to these values:
      | Wiki name | Wiki 2 |
      | Description  | Test wiki description 2 |
      | First page name | First page |
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

    Then I should not see "Wiki 1" in the "region-main" "region"
    Then I should see "Wiki 2" in the "region-main" "region"

    When I follow "Wiki 2"
    And I follow "Course 1"

    Then I should not see "Wiki 2" in the "region-main" "region"
