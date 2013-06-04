<?php
/**
 * Contains the SlotContinuousTest class
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Test\Data;

use Orukusaki\VisualRadius\Data\Session;
use Orukusaki\VisualRadius\Data\SlotContinuous;

/**
 * Tests for VisualRadius\Data\SlotContinuous
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class SlotContinuousTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Orukusaki\VisualRadius\Data\SlotContinuous
     */
    public function testConstructorAndGetters()
    {
        $days = 1;
        $service = Session::SERVICE_BROADBAND;

        $slotContinuous = new SlotContinuous($days, $service);

        $this->assertEquals($days, $slotContinuous->getDays());
        $this->assertEquals($service, $slotContinuous->getService());

        $slotContinuous->incDays();

        $this->assertEquals($days + 1, $slotContinuous->getDays());
    }
}
