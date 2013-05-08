<?php
/**
 * Contains the SessionMarker class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Data;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Int;
use Doctrine\ODM\MongoDB\Mapping\Annotations\String;

/**
 * Session Marker
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
abstract class SessionMarker
{

    /**
     * @Int
     */
    protected $time;

    /**
     * @String
     */
    protected $service;

    public function __construct($time, $service)
    {
        $this->time = $time;
        $this->service = $service;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getService()
    {
        return $this->service;
    }
}