<?php
/**
 * Contains the SessionCloseTest class
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Test\Data;

use Orukusaki\VisualRadius\Data\SessionClose;
use Orukusaki\VisualRadius\Data\Session;

/**
 * Tests for VisualRadius\Data\SessionClose
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class SessionCloseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Orukusaki\VisualRadius\Data\SessionClose
     * @covers Orukusaki\VisualRadius\Data\SessionMarker
     */
    public function testConstructorAndGetters()
    {
        $time = 12345;
        $service = Session::SERVICE_BROADBAND;

        $sessionClose = new SessionClose($time, $service);

        $this->assertEquals($time, $sessionClose->getTime());
        $this->assertEquals($service, $sessionClose->getService());
    }
}
