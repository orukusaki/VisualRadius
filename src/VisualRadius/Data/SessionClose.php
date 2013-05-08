<?php
/**
 * Contains the SessionClose class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Data;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;

/**
 * Marker indicating the end of a session
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @EmbeddedDocument
 */
class SessionClose extends SessionMarker
{
}
