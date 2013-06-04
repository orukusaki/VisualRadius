<?php
/**
 * Contains the SessionBox class
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
 * Box indicating time online during a session
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @EmbeddedDocument
 */
class SessionBox
{
    /**
     * @var int
     * @Int
     */
    private $start;

    /**
     * @var int
     * @Int
     */
    private $end;

    /**
     * @var string
     * @String
     */
    private $service;

    public function __construct($start, $end, $service)
    {
        $this->start = $start;
        $this->end = $end;
        $this->service = $service;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getService()
    {
        return $this->service;
    }
}