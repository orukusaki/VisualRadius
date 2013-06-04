<?php
/**
 * Contains the SessionTest class
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Test\Data;

use Orukusaki\VisualRadius\Data\Session;
use DateTime;

/**
 * Tests for VisualRadius\Data\Session
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test That the Correct Status is selected
     *
     * @param DateTime $start      Start Time
     * @param DateTime $close      Close time
     * @param int      $last       Last Update Time
     * @param string   $service    Service Type
     * @param string   $expStatus  Expected Status
     * @param int      $expStart   Expected Start Time
     * @param int      $expLast    Expected Last Update Time
     * @param string   $expService Expected Service Type
     *
     * @covers Orukusaki\VisualRadius\Data\Session
     * @dataProvider provideSomeSessionData
     *
     * @return void
     */
    public function testStatusIsSetUpCorrectly(
        $start,
        $close,
        $last,
        $service,
        $expStatus,
        $expStart,
        $expLast,
        $expService
    ) {
        $session = new Session($service, $last, $start, $close);

        $this->assertEquals($expStatus, $session->getStatus());
        $this->assertEquals($expStart, $session->getStart());
        $this->assertEquals($expLast, $session->getLast());
        $this->assertEquals($expService, $session->getService());
    }

    /**
     * Data Provider for testStatusIsSetUpCorrectly
     *
     * @return array
     */
    public function provideSomeSessionData()
    {
        $oneDayAgo = new DateTime("-1 day");
        $oneMinuteAgo = new DateTime("-1 minute");
        $fiveHoursAgo = new DateTime("-5 hours");
        $now = new DateTime();

        return array(
            // Data set 0, active session
            array(
                $oneDayAgo,                 // start
                null,                       // close
                $oneMinuteAgo,              // last
                Session::SERVICE_BROADBAND, // service
                Session::STATUS_ACTIVE,     // expStatus
                $oneDayAgo,                 // expStart
                $now,                       // expLast
                Session::SERVICE_BROADBAND, // expService
            ),
            // Data set 1, closed session
            array(
                $oneDayAgo,                 // start
                $oneMinuteAgo,              // close
                $oneMinuteAgo,              // last
                Session::SERVICE_BROADBAND, // service
                Session::STATUS_CLOSED,     // expStatus
                $oneDayAgo,                 // expStart
                $oneMinuteAgo,              // expLast
                Session::SERVICE_BROADBAND, // expService
            ),
            // Data set 2, lost session
            array(
                $oneDayAgo,                 // start
                null,                       // close
                $fiveHoursAgo,              // last
                Session::SERVICE_BROADBAND, // service
                Session::STATUS_LOST,       // expStatus
                $oneDayAgo,                 // expStart
                $fiveHoursAgo,              // expLast
                Session::SERVICE_BROADBAND, // expService
            ),
            // Data set 3, failed session
            array(
                null,                      // start
                null,                       // close
                $fiveHoursAgo,              // last
                Session::SERVICE_BROADBAND, // service
                Session::STATUS_FAILED,     // expStatus
                $fiveHoursAgo,              // expStart
                $fiveHoursAgo,              // expLast
                Session::SERVICE_BROADBAND, // expService
            ),
            // Data set 4, closed dialup session
            array(
                $oneDayAgo,                 // start
                $oneMinuteAgo,              // close
                $oneMinuteAgo,              // last
                Session::SERVICE_DIALUP,    // service
                Session::STATUS_CLOSED,     // expStatus
                $oneDayAgo,                 // expStart
                $oneMinuteAgo,              // expLast
                Session::SERVICE_DIALUP,    // expService
            ),
        );
    }
}
