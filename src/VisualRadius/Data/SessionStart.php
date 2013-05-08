<?php
/**
 * Contains the SessionStart class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Data;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;

/**
 * Marker indicating the start of a session
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @EmbeddedDocument
 */
class SessionStart extends SessionMarker
{
}