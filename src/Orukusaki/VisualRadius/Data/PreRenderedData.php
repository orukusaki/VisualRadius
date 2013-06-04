<?php
/**
 * Contains the PreRenderedData class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Data;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedMany;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Date;

/**
 * Class for pre-rendering session data to a map of objects to draw
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @Document
 */
class PreRenderedData
{
    /**
     * @var int
     * @Id
     */
    private $id;

    /**
     * List of slots to draw
     *
     * @var array
     * @EmbedMany
     */
    private $slots = array();

    /**
     * @var DateTime
     * @Date
     */
    private $lastAccessed;

    /**
     * Build From Session Data
     *
     * @param SessionList $sessions Sessions
     *
     * @return PreRenderedData
     */
    public static function buildFromSessionData(SessionList $sessions)
    {
        //Take all the sessions and place them in an array 'map' of the image to draw.

        $data = new self;
        $slots = &$data->slots;
        $map = array();

        foreach ($sessions as $session) {

            if ($session->getStatus() == Session::STATUS_FAILED) {

                $date = clone $session->getLast();
                $lastTimeMinutes = self::getTimeInMinutes($session->getLast());
                $map[$date->format('Y-m-d')][] = new SessionFailed($startTimeMinutes, $session->getService());

            } else {

                $startDate = clone $session->getStart();
                $startDate->modify('midnight');

                $startTimeMinutes = self::getTimeInMinutes($session->getStart());

                $map[$startDate->format('Y-m-d')][] = new SessionStart($startTimeMinutes, $session->getService());

                $lastDate = clone $session->getLast();

                $lastTimeMinutes = self::getTimeInMinutes($session->getLast());

                if ($session->getStatus() == Session::STATUS_CLOSED) {
                    $map[$lastDate->format('Y-m-d')][] = new SessionClose($lastTimeMinutes, $session->getService());
                } else {
                    $map[$lastDate->format('Y-m-d')][] = new SessionOpenEnd($lastTimeMinutes, $session->getService());
                }

                $lastDate->modify('midnight +1 day -1 second');
                for ($date = clone $startDate; $date <= $lastDate; $date->modify('+1 day')) {

                    if ($date->format('Y-m-d') == $startDate->format('Y-m-d')) {
                        $boxStartMinutes = $startTimeMinutes;

                    } else {

                        $boxStartMinutes = 0;

                    } if ($date->format('Y-m-d') == $lastDate->format('Y-m-d')) {

                        $boxEndMinutes = $lastTimeMinutes;

                    } else {
                        $boxEndMinutes = 24 * 60;
                    }

                    $map[$date->format('Y-m-d')][] = new SessionBox($boxStartMinutes, $boxEndMinutes, $session->getService());
                }
            }
        }
        // For each day, we need to know whether it contains session data,
        // is empty, or contains only part of a continuous connection.

        if (empty($map)) {
            return $data;
        }

        $days=array_keys($map);
        rsort($days);
        $lastDate = DateTime::createFromFormat('Y-m-d', current($days));
        $firstDate = DateTime::createFromFormat('Y-m-d', $days[sizeof($days)-1]);
        $i=0;
        // for each day between the first and last.


        for ($date = $firstDate; $date <= $lastDate; $date->modify('+1 day')) {
            $slot = new SlotDraw(clone $date);
            if (array_key_exists($date->format('Y-m-d'), $map)) {
                $slot->addObjects($map[$date->format('Y-m-d')]);
            }
            $slots[$i++] = $slot;

        }

        return $data;
    }

    public function getSlots()
    {
        return is_object($this->slots)? $this->slots->toArray() : $this->slots;
    }

    public function setSlots(array $slots)
    {
        $this->slots = $slots;
    }

    public function getId()
    {
        return $this->id;
    }

    public function updateLastAccess()
    {
        $this->lastAccessed = new DateTime();
    }

    private function getTimeInMinutes(DateTime $time)
    {
        return ($time->format("G") * 60) + $time->format("i");
    }
}
