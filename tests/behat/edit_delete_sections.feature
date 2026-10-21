@format @format_onetopic
Feature: Sections can be edited and deleted in Onetopic format
  In order to rearrange my course contents
  As a teacher
  I need to edit and delete sections

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format   | coursedisplay | numsections | initsections |
      | Course 1 | C1        | onetopic | 0             | 5           | 1            |
      | Course 2 | C2        | onetopic | 0             | 1           | 0            |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    | section |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     | 0       |
      | book       | Test book name         |                               | C1     | book1       | 1       |
      | lesson     | Test lesson name       | Test lesson description       | C1     | lesson1     | 4       |
      | choice     | Test choice name       | Test choice description       | C1     | choice1     | 5       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |
    And I log in as "teacher1"

  Scenario: View the default name of the general section in Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    When I edit the section "0"
    Then the field "Section name" matches value ""
    And I should see "General"

  Scenario: Edit the default name of the general section in Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    And I should see "General" in the "region-main" "region"
    When I edit the section "0" and I fill the form with:
      | Section name      | This is the general section |
    Then I should see "This is the general section" in the "page" "region"

  Scenario: View the default name of the second section in Onetopic format
    Given I am on "Course 2" course homepage with editing mode on
    When I click on "Topic 1" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I edit the section "1"
    Then the field "Section name" matches value ""
    And I should see "Topic 1"

  Scenario: Edit section summary in Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    When I click on "Section 2" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I edit the section "2" and I fill the form with:
      | Description | Welcome to section 2 |
    Then I should see "Welcome to section 2" in the "page" "region"

  Scenario: Edit section default name in Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    When I click on "Section 2" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I edit the section "2" and I fill the form with:
      | Section name      | This is the second section |
    Then I should see "This is the second section" in the "page" "region"
    And I should not see "Section 2" in the "region-main" "region"

  Scenario: Deleting the last section in Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    When I click on "Section 5" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I delete section "5"
    Then I should see "This will delete Section 5 and all the activities it contains."
    And I press "Delete"
    And I should not see "Section 5"
    And I should see "Section 4"

  Scenario: Deleting the middle section in Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    When I click on "Section 4" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I delete section "4"
    And I press "Delete"
    Then I should not see "Section 4"
    And I should see "Section 5"
    And I should not see "Test lesson name"
    And I click on "Section 5" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I should see "Test choice name" in the "region-main" "region"

  Scenario: Adding sections at the end of a Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    When I click on "Section 5" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I follow "Add a section after the currently selected section"
    Then I should see "Topic 6" in the "region-main" "region"
    And I click on "Section 5" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I should see "Test choice name" in the "region-main" "region"

  Scenario: Adding sections between in Onetopic format
    Given I am on "Course 1" course homepage with editing mode on
    When I click on "Section 4" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I follow "Add a section after the currently selected section"
    Then I should see "Topic 5" in the "region-main" "region"
    And I click on "Section 5" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I should see "Test choice name" in the "region-main" "region"
    And I click on "Topic 5" "link" in the "#page-content ul.nav.nav-tabs" "css_element"
    And I should not see "Test choice name" in the "region-main" "region"
