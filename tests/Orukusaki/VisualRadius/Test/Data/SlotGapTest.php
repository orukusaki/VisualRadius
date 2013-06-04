<?php
/**
 * Contains the SlotGapTest class
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Test\Data;

use Orukusaki\VisualRadius\Data\SlotGap;

/**
 * Tests for VisualRadius\Data\SlotGap
 *
 * @package    VisualRadius
 * @subpackage Test
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class SlotGapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Orukusaki\VisualRadius\Data\SlotGap
     */
    public function testConstructorAndGetters()
    {
        $days = 1;

        $slotGap = new SlotGap($days);

        $this->assertEquals($days, $slotGap->getDays());

        $slotGap->incDays();

        $this->assertEquals($days + 1, $slotGap->getDays());
    }
}
