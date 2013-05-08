<?php
/**
 * Contains the SessionOpenEnd class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Data;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;

/**
 * Marker indicating the open end of a session (one which was lost)
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @EmbeddedDocument
 */
class SessionOpenEnd extends SessionMarker
{
}