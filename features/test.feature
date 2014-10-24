Feature: foo

Scenario: foo
  When I go to "/"
  And I follow "link-create"
  Then I should be on "/create"
