Feature: Generate a (non-cached) image
  Submit some record data to create an image without saving it.

  Scenario: Create an image
    Given I am on "/"
    And I will accept "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
    Then I fill in "pastedRecords" with:
    """
    Subscription    Access Rack Last Event  Event Time  Message IP Assigned Service Calling From    Session Started Session Ended   Session Duration
Plusnet Value   195.166.130.13  active  15:59 11/May/2012   unknown (Interim update)    84.92.162.43    Not set BBEU01968462    17:58 09/May/2012   N/A  One Day, 22:6:54 (on going)
Plusnet Value   195.166.128.87  ended   17:58 09/May/2012   unknown (Termination by server side (NAS) Modem)    84.92.162.43    Not set BBEU01968462    11:20 09/May/2012   17:58 09/May/2012     6:38:19
Plusnet Value   195.166.130.37  ended   11:20 09/May/2012   unknown (User request)  84.92.162.43    Not set BBEU01968462    07:36 09/May/2012   11:20 09/May/2012     3:44:13
    """
    When I press "submit"
    Then a success response should be recieved
    And the response format should be "image/png"