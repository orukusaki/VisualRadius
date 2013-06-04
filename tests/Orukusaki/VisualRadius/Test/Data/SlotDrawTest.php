<?php
/**
 * Contains the SlotDrawTest class
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Test\Data;

use DateTime;
use Orukusaki\VisualRadius\Data\Session;
use Orukusaki\VisualRadius\Data\SessionStart;
use Orukusaki\VisualRadius\Data\SessionBox;
use Orukusaki\VisualRadius\Data\SessionOpenEnd;
use Orukusaki\VisualRadius\Data\SlotDraw;

/**
 * Tests for VisualRadius\Data\SlotDraw
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class SlotDrawTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Orukusaki\VisualRadius\Data\SlotDraw
     */
    public function testConstructorAndGetDate()
    {
        $date = new DateTime;

        $slotDraw = new SlotDraw($date);

        $this->assertEquals($date, $slotDraw->getDate());
    }

    /**
     * @covers Orukusaki\VisualRadius\Data\SlotDraw
     */
    public function testGetObjectsWithNoFilter()
    {
        $date = new DateTime;

        $objects = array(
            new SessionBox(1, 2, Session::SERVICE_BROADBAND),
            new SessionStart(1, Session::SERVICE_BROADBAND),
            new SessionOpenEnd(2, Session::SERVICE_BROADBAND),
        );

        $slotDraw = new SlotDraw($date);
        $slotDraw->addObjects($objects);

        $this->assertEquals($objects, $slotDraw->getObjects());
    }

    /**
     * @covers Orukusaki\VisualRadius\Data\SlotDraw
     */
    public function testGetObjectsWithFilter()
    {
        $date = new DateTime;

        $objects = array(
            new SessionBox(1, 2, Session::SERVICE_BROADBAND),
            new SessionStart(1, Session::SERVICE_BROADBAND),
            new SessionOpenEnd(2, Session::SERVICE_BROADBAND),
            new SessionBox(3, 4, Session::SERVICE_DIALUP),
            new SessionStart(3, Session::SERVICE_DIALUP),
            new SessionOpenEnd(4, Session::SERVICE_DIALUP),
        );

        $expectedObjects = array(
            new SessionStart(1, Session::SERVICE_BROADBAND),
            new SessionStart(3, Session::SERVICE_DIALUP),
        );

        $slotDraw = new SlotDraw($date);
        $slotDraw->addObjects($objects);

        $this->assertEquals($expectedObjects, $slotDraw->getObjects('Start'));
    }
}
