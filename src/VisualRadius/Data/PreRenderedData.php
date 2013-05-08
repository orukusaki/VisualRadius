<?php
/**
 * Contains the PreRenderedData class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Data;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedMany;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

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
    protected $slots = array();

    /**
     * Build From Session Data
     *
     * //TODO: Move from timestamps to DateTime
     *
     * @return PreRenderedData
     */
    public static function buildFromSessionData(SessionList $sessions, $options)
    {
        //Take all the sessions and place them in an array 'map' of the image to draw.

        $data = new self;
        $slots = &$data->slots;
        $map = array();

        foreach ($sessions as $session) {

            if (in_array($session->getStatus(), array(Session::STATUS_START, Session::STATUS_FAILED))) {

                $date = clone $session->getLast();

                $lastTimeMinutes = self::getTimeInMinutes($session->getLast());
                switch ($session->getStatus()) {
                    case Session::STATUS_START:
                        $map[$date->format('Y-m-d')][] = new SessionStart($startTimeMinutes, $session->getService());
                        break;
                    case Session::STATUS_FAILED:
                        $map[$date->format('Y-m-d')][] = new SessionFailed($startTimeMinutes, $session->getService());
                        break;
                }
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
                        $boxEndMinutes = $options['viewEnd'] * 60;
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

        if ($options['condense']) {
            for ($date = $firstDate; $date <= $lastDate; $date->modify('+1 day')) {
                if (!array_key_exists($date->format('Y-m-d'), $map)) { // There were no sessions today.

                    if ($i>0 and $slots[$i-1] instanceof SlotGap) {
                        $slots[$i-1]->incDays();
                    } else {
                        $slots[$i++] = new SlotGap(1);
                    }
                } elseif (sizeof($map[$date->format('Y-m-d')]) == 1 && $map[$date->format('Y-m-d')][0] instanceof SessionBox) {
                    // There's only one thing in this array and it's a continuous connection
                    if ($i>0 and $slots[$i-1] instanceof SlotContinuous) {
                        $slots[$i-1]->incDays();
                    } else {
                        $slots[$i++] = new SlotContinuous(1, $map[$date->format('Y-m-d')][0]->getService());
                    }
                } else {
                    $slot = new SlotDraw(clone $date);
                    $slot->addObjects($map[$date->format('Y-m-d')]);
                    $slots[$i++] = $slot;
                }
            }
        } else {
            for ($date = $firstDate; $date <= $lastDate; $date->modify('+1 day', $date)) {
                $slot = new SlotDraw(clone $date);
                $slot->addObjects($map[$date->format('Y-m-d')]);
                $slots[$i++] = $slot;

            }
        }

        return $data;
    }

    public function getSlots()
    {
        return is_object($this->slots)? $this->slots->toArray() : $this->slots;
    }

    public function getId()
    {
        return $this->id;
    }

    private function getTimeInMinutes(DateTime $time)
    {
        return ($time->format("G") * 60) + $time->format("i");
    }
}
