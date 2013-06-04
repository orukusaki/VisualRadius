<?php
/**
 * Contains the SessionBoxTest class
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Test\Data;

use Orukusaki\VisualRadius\Data\SessionBox;
use Orukusaki\VisualRadius\Data\Session;

/**
 * Tests for VisualRadius\Data\SessionBox
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class SessionBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Orukusaki\VisualRadius\Data\SessionBox
     */
    public function testConstructorAndGetters()
    {
        $start = 12345;
        $end = 23456;
        $service = Session::SERVICE_BROADBAND;

        $sessionBox = new SessionBox($start, $end, $service);

        $this->assertEquals($start, $sessionBox->getStart());
        $this->assertEquals($end, $sessionBox->getEnd());
        $this->assertEquals($service, $sessionBox->getService());
    }
}
