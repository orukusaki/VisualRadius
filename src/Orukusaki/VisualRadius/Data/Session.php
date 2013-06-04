<?php
/**
 * Contains the Session class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @link       Link
 */
namespace Orukusaki\VisualRadius\Data;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Int;
use Doctrine\ODM\MongoDB\Mapping\Annotations\String;
use DateTime;

/**
 * Represents a single session record
 *
 * 5 Possible session statuses, Ended, Active, Lost, start, and failed.
 * If the starttime is invalid, then the session didn't start properly .
 * If a close time is set, then the session has ended properly.
 * If there is no close time, but the last update was within the last 4 hours
 * then the session should be considered active.
 * If there is no close time and the last update was more than 4 hours ago,
 * then we consider the session lost.
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @link       Link
 * @EmbeddedDocument
 */
class Session
{
    const STATUS_CLOSED = 'closed';
    const STATUS_ACTIVE = 'active';
    const STATUS_LOST   = 'lost';
    const STATUS_FAILED = 'failed';

    const SERVICE_BROADBAND = 'broadband';
    const SERVICE_DIALUP    = 'dial';

    /**
     * @var string
     * @String
     */
    private $status;

    /**
     * @var int
     * @Date
     */
    private $start;

    /**
     * @var int
     * @Date
     */
    private $last;

    /**
     * @var string
     * @String
     */
    private $service;

    /**
     * Constructor
     *
     * @param string   $service Service Type
     * @param DateTime $last    Last Update
     * @param DateTime $start   Start Time
     * @param DateTime $close   Close Time
     *
     * return void
     */
    public function __construct($service, DateTime $last, DateTime $start = null, DateTime $close = null)
    {
        $this->start = $start;
        $this->last = $last;
        $this->service = $service;

        if (is_null($start)) {
            $this->start = $last;
            $this->status = self::STATUS_FAILED;

        } elseif (!is_null($close)) {

            $this->status = self::STATUS_CLOSED;
            $this->last = $close;

        } elseif ($last >= new DateTime("-4 hours")) {

            $this->status = self::STATUS_ACTIVE;
            $this->last = new DateTime();
        } else {
            $this->status = self::STATUS_LOST;
        }
    }

    /**
     * Accessor for _status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Accessor for _start
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Accessor for _last
     *
     * @return int
     */
    public function getlast()
    {
        return $this->last;
    }

    /**
     * Accessor for _service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }
}
