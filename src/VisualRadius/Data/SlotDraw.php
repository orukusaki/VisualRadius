<?php
/**
 * Contains the VisualRadiusdataSlotDraw class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Data;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedMany;

/**
 * A Slot which contains objects to draw
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @EmbeddedDocument
 */
class SlotDraw
{
    /**
     * Date represented by the slot
     *
     * @var DateTime
     * @Date
     */
    protected $date;

    /**
     * Objects to display
     *
     * @var array
     * @EmbedMany(
     *      discriminatorMap={
     *          "session"="Session",
     *          "box"="SessionBox",
     *          "close"="SessionClose",
     *          "failed"="SessionFailed",
     *          "openend"="SessionOpenEnd",
     *          "start"="SessionStart"
     *      }
     * )
     */
    private $objects = array();

    /**
     * Constructor
     *
     * @param DateTime $date Date
     *
     * @return void
     */
    public function __construct(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Accessor for Date
     *
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add objects to draw
     *
     * @param array $objects Objects to add
     *
     * @return SlotDraw
     */
    public function addObjects($objects)
    {
        $this->objects = array_merge($objects);
        return $this;
    }

    /**
     * Get Objects of a certain type
     *
     * @param string $type Optional, the type of object to filter by
     *
     * @return array
     */
    public function getObjects($type = null)
    {
        if (is_null($type)) {
            return $this->objects;
        }

        $objectsToReturn = array();

        $classname = '\VisualRadius\Data\Session' . $type;

        foreach ($this->objects as $object) {
            if ($object instanceof $classname) {
                $objectsToReturn[] = $object;
            }
        }
        return $objectsToReturn;
    }
}
