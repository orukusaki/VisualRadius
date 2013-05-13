<?php
namespace VisualRadius\Decorator;

use VisualRadius\Data\PreRenderedData;
use VisualRadius\Data\SlotDraw;
use VisualRadius\Data\SlotGap;
use VisualRadius\Data\SlotContinuous;
use VisualRadius\Data\SessionBox;

class Condense implements DecoratorInterface
{
    public function decorate(PreRenderedData $data)
    {

        $slots = $data->getSlots();

        usort(
            $slots,
            function (SlotDraw $a, SlotDraw $b) {
                return ($a->getDate() < $b->getDate()) ? -1: 1;
            }
        );

        $newSlots = array();

        foreach ($slots as $slot) {

            $objects = $slot->getObjects();
            $last = end($newSlots);
            // There were no sessions today
            if (empty($objects)) {
                if ($last  instanceof SlotGap) {
                    $last->incDays();
                } else {
                    $newSlots[] = new SlotGap(1);
                }

                continue;
            }

            // There's only a continuous box in this slot
            if (sizeof($objects) == 1 && $objects[0] instanceof SessionBox) {

                if ($last instanceof SlotContinuous) {
                    $last ->incDays();
                } else {
                    $newSlots[] = new SlotContinuous(1, $objects[0]->getService());
                }
                continue;
            }

            $newSlots[] = $slot;
        }

        $data ->setSlots($newSlots);
    }
}
