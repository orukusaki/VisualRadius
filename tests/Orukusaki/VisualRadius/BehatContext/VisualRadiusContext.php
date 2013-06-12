<?php
namespace Orukusaki\VisualRadius\BehatContext;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ExpectationException;
class VisualRadiusContext extends MinkContext
{
    /**
     * @Given /^I will accept "([^"]*)"$/
     */
    public function iWillAccept($accept)
    {
        $this->getSession()->setRequestHeader('Accept', $accept);
    }

    /**
     * @Then /^the response format should be "([^"]*)"$/
     */
    public function theResponseFormatShouldBe($expected)
    {
        $session = $this->getSession();
        $headers = $session->getResponseHeaders();

        if (!array_key_exists('content-type', $headers)) {
            $message = sprintf('Expected Content-type "%s", but no content type was set.', $expected);
            throw new ExpectationException($message, $session);
        }

        if (strpos($headers['content-type'], $expected) === false) {
            $message = sprintf('Expected Content-type "%s", but got "%s"', $expected, $headers['content-type']);
            throw new ExpectationException($message, $session);
        }
    }

    /**
     * @Then /^a success response should be recieved$/
     */
    public function theASuccessResponseShouldBeRecieved()
    {
        $this->assertSession()->statusCodeEquals(200);
    }
}
