<?php
/**
 * Contains the SessionListTest class
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Test\Data;

use Orukusaki\VisualRadius\Data\SessionList;
use Orukusaki\VisualRadius\Data\Session;
use DateTime;

/**
 * Tests for VisualRadius\Data\SessionList
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class SessionListTest extends \PHPUNIT_Framework_TestCase
{
    /**
     * Check that a Session List behaves nicely as an Itterator
     *
     * @covers Orukusaki\VisualRadius\Data\SessionList
     *
     * @return void
     */
    public function testSessionListItteratorFunctions()
    {
        $oneDayAgo = new DateTime("-1 day");
        $oneMinuteAgo = new DateTime("-1 minute");
        $fiveHoursAgo = new DateTime("-5 hours");
        $now = new DateTime();

        $list = new SessionList();

        $session0 = new Session(Session::SERVICE_BROADBAND, $oneMinuteAgo, $oneDayAgo, $oneMinuteAgo);
        $list->add($session0);
        $session1 = new Session(Session::SERVICE_BROADBAND, $oneDayAgo, $oneMinuteAgo, null);
        $list->add($session1);
        $session2 = new Session(Session::SERVICE_DIALUP, $oneDayAgo, null, $oneMinuteAgo);
        $list->add($session2);

        foreach ($list as $idx => $session) {
            $this->assertEquals(${'session'.$idx}, $session);
        }
    }
}
