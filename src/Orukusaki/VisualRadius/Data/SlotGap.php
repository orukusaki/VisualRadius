<?php
/**
 * Contains the SlotGap class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Data;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Int;

/**
 * A Slot which represents a gap where there were no sessions
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @EmbeddedDocument
 */
class SlotGap
{
    /**
     * Number of days which the gap covers
     *
     * @var int
     * @Int
     */
    private $days = 0;

    /**
     * Constructor
     *
     * @param int $days Number of days
     *
     * @return void
     */
    public function __construct($days = 0)
    {
        $this->days = $days;
    }

    /**
     * Increment day count
     *
     * @return SlotGap
     */
    public function incDays()
    {
        $this->days++;
        return $this;
    }

    /**
     * Get Days
     *
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }
}
