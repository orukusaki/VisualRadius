<?php
/**
 * Contains the VisualRadiusDataSlotContinuous class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Data;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Int;
use Doctrine\ODM\MongoDB\Mapping\Annotations\String;

/**
 * A Slot which contains a continuous connection
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @EmbeddedDocument
 */
class SlotContinuous
{
    /**
     * Number of DAys
     *
     * @var int
     * @Int
     */
    protected $days = 0;

    /**
     * Service
     *
     * @var string
     * @String
     */
    protected $service;

    /**
     * Constructor
     *
     * @param int    $days    Days
     * @param string $service Service
     *
     * @return void
     */
    public function __construct($days, $service)
    {
        $this->days = $days;
        $this->service = $service;
    }

    /**
     * Increment day count
     *
     * @return SlotContinuous
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

    /**
     * Get Service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }
}
