<?php
/**
 * Contains the Gd class
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Renderer;

use DateTime;
use Orukusaki\VisualRadius\Data\PreRenderedData;
use Orukusaki\VisualRadius\Data\SessionBox;
use Orukusaki\VisualRadius\Data\SessionClose;
use Orukusaki\VisualRadius\Data\SessionFailed;
use Orukusaki\VisualRadius\Data\SessionList;
use Orukusaki\VisualRadius\Data\SessionOpenEnd;
use Orukusaki\VisualRadius\Data\SessionStart;
use Orukusaki\VisualRadius\Data\SlotContinuous;
use Orukusaki\VisualRadius\Data\SlotDraw;
use Orukusaki\VisualRadius\Data\SlotGap;

/**
 * Renderer generates a png image using the gd library
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class Gd implements RendererInterface
{
    protected $image;
    protected $options = array();

    /**
     * Constructor
     *
     * @param ArrayAccess $container Container
     *
     * @return void
     */
    public function __construct(\ArrayAccess $container)
    {
        $options = $container['image'];
        $options['viewLength'] = $options['viewEnd'] - $options['viewStart'];
        $options['gridRight'] = $options['imageWidth'] - $options['borderRight'];
        $options['hourstep'] = ($options['gridRight'] - $options['borderLeft']) / $options['viewLength'];
        $options['gridWidth'] = $options['imageWidth'] - $options['borderRight'] - $options['borderLeft'];

        $this->options = array_merge($container['cache'], $options);
    }

    /**
     * Get Content Header
     *
     * @return array
     */
    public function getContentHeader()
    {
        return array("Content-type" => "image/png");
    }

    /**
     * Render
     *
     * @param PreRenderedData $data Data to render
     *
     * @return Closure
     */
    public function render(PreRenderedData $data)
    {
        $encode = array_key_exists('base64', $this->options) ? $this->options['base64'] : false;

        if ($id = $data->getId()) {

            $filename = $this->options['path'] . DIRECTORY_SEPARATOR . $data->getId() . '.png';

            if (file_exists($filename)) {
                return function () use ($filename) {
                        readfile($filename);
                };
            }
        }

        $image = $this->generateImage($data);

        if ($id) {
            imagepng($image, $filename);
        }

        return function () use ($image, $encode) {
            if ($encode) {
                ob_clean();
                ob_start();
                imagepng($image);
                $ret = ob_get_clean();
                echo base64_encode($ret);
            } else {
                imagepng($image);
            }
        };
    }

    /**
     * Generate an image
     *
     * @param PreRenderedData $data Pre-Rendered Data
     *
     * @return void
     */
    protected function generateImage(PreRenderedData $data)
    {
        //TODO: get rid of this line and be more explicit here
        extract($this->options);
        $lastDate = new DateTime();
        $slots = array_reverse($data->getSlots());

        $imageHeight = $borderTop + $borderBottom + ($slotHeight * sizeof($slots));

        $image = imagecreatetruecolor($imageWidth, $imageHeight);

        $colourLine       = $this->makeColour($image, $colours['line']);
        $colourBackground = $this->makeColour($image, $colours['background']);
        $weekdayBack      = $this->makeColour($image, $colours['weekdayback']);
        $weekendBack      = $this->makeColour($image, $colours['weekendback']);
        $vertline         = $this->makeColour($image, $colours['vertline']);
        $keyback          = $this->makeColour($image, $colours['keyback']);

        $sprites = array();
        $sprites['Close']['broadband']   = imagecreatefrompng($imagePath . '/barclose.png');
        $sprites['Mid']['broadband']     = imagecreatefrompng($imagePath . '/barmid.png');
        $sprites['Failed']['broadband']  = imagecreatefrompng($imagePath . '/barreject.png');
        $sprites['OpenEnd']['broadband'] = imagecreatefrompng($imagePath . '/barroundend.png');
        $sprites['Start']['broadband']   = imagecreatefrompng($imagePath . '/barstart.png');
        $sprites['Close']['dialup']      = imagecreatefrompng($imagePath . '/barclosedial.png');
        $sprites['Mid']['dialup']        = imagecreatefrompng($imagePath . '/barmiddial.png');
        $sprites['Failed']['dialup']     = imagecreatefrompng($imagePath . '/barrejectdial.png');
        $sprites['OpenEnd']['dialup']    = imagecreatefrompng($imagePath . '/barroundenddial.png');
        $sprites['Start']['dialup']      = imagecreatefrompng($imagePath . '/barstartdial.png');

        imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $colourBackground);

        // Draw Grid and dates.
        foreach ($slots as $i => $slot) {
            if ($slot instanceof SlotDraw) {

                $date = $slot->getDate();
                $lastDate = $date;

            } elseif ($slot->getDays() == 1 && isset($slots[$i-1])) {

                $date = clone $slots[$i-1]->getDate();
                $date->sub(new \DateInterval('P1D'));

            } else {

                $date = false;
            }
            if ($date) {
                $day = $date->format("D");
                if ($day=="Sat" or $day=="Sun") {
                    $colour = $weekendBack;
                } else {
                    $colour = $weekdayBack;
                }
                imagefilledrectangle(
                    $image,
                    5,
                    $borderTop + ($i * $slotHeight),
                    $imageWidth - 10,
                    $borderTop + ($i * $slotHeight) + 38,
                    $colour
                );
                imageline(
                    $image,
                    $borderLeft,
                    $borderTop + ($i * $slotHeight) + 35,
                    $gridRight,
                    $borderTop + ($i * $slotHeight) + 35,
                    $colourLine
                );
                imagestring($image, 2, 18, $borderTop + ($i * $slotHeight) + 10, $day, $colourLine);
                imagestring($image, 2, 8, $borderTop + ($i * $slotHeight) + 20, $date->format('d/m/y'), $colourLine);

            } else {
                $colour = $weekdayBack;
                imagefilledrectangle(
                    $image,
                    5,
                    $borderTop + ($i * $slotHeight),
                    $imageWidth - 10,
                    $borderTop + ($i * $slotHeight) + 38,
                    $colour
                );
                imageline(
                    $image,
                    $borderLeft,
                    $borderTop + ($i * $slotHeight) + 35,
                    $gridRight,
                    $borderTop + ($i * $slotHeight) + 35,
                    $colourLine
                );
            }

            for ($j = 0; $j <= $viewLength; $j++) {
                imageline(
                    $image,
                    $borderLeft + ($j * $hourstep),
                    $borderTop  + ($i * $slotHeight) + 35,
                    $borderLeft + ($j * $hourstep),
                    $borderTop  + ($i * $slotHeight) + 32,
                    $colourLine
                );
            }

            // Vertical lines drawn at midnight, 6am, 12pm and 6pm  (provided they are in the image)
            for ($j = 0; $j <= 24; $j = $j + 6) {
                if ($j >= $viewStart / 60  && $j <= $viewEnd) {
                    imageline(
                        $image,
                        $borderLeft + (($j-($viewStart))*$hourstep),
                        $borderTop+($i*$slotHeight),
                        $borderLeft+(($j-($viewStart))*$hourstep),
                        $borderTop+($i*$slotHeight)+39,
                        $vertline
                    );
                }
            }

            // Also draw a vertical line at the start and the end of the box, just cos it looks wrong otherwise.
            imageline(
                $image,
                $borderLeft,
                $borderTop + ($i * $slotHeight),
                $borderLeft,
                $borderTop + ($i * $slotHeight) + 39,
                $vertline
            );
            imageline(
                $image,
                $gridRight,
                $borderTop + ($i * $slotHeight),
                $gridRight,
                $borderTop + ($i * $slotHeight) + 39,
                $vertline
            );

        }

        // Draw Hour column headers
        for ($j = 0; $j <= $viewLength; $j++) {

            imagestring($image, 1, $borderLeft + ($j * $hourstep) - 3, 10, $j + $viewStart , $colourLine);
        }

        //Blank out any time after now, by drawing a rectangle with the same colour as the bg.
        $now = new DateTime();
        if ($lastDate->format('Ymd') == $now->format('Ymd')) {
            $nowMinutes = ($now->format("G") * 60) + $now->format("i");
            if ($nowMinutes >= $viewStart && $nowMinutes <= $viewEnd) {
                $nowPixels = $borderLeft + (($nowMinutes - $viewStart) / $viewLength) * ($gridRight - $borderLeft);
                imagefilledrectangle($image, $nowPixels, $borderTop, $imageWidth-10, $borderTop+38, $colourBackground);
            }
        }

        foreach ($slots as $i => $slot) {

            // Start drawing the sessions into the image
            if ($slot instanceof SlotDraw) {
                foreach ($slot->getObjects('Box') as $entity) {
                    $boxStartMinutes = max($entity->getStart(), $viewStart * 60);
                    $boxEndMinutes   = min($entity->getEnd(), $viewEnd * 60);
                    $boxStartPixels  = $this->getHPosByTime($boxStartMinutes);
                    $boxEndPixels    = $this->getHPosByTime($boxEndMinutes);
                    for ($j = $boxStartPixels; $j <= $boxEndPixels; $j++) {

                        imagecopy(
                            $image,
                            $sprites['Mid'][$entity->getService()],
                            $j,
                            $borderTop+($i*$slotHeight)-3,
                            0,
                            0,
                            1,
                            45
                        );
                    }

                }

                foreach (array('Close', 'OpenEnd', 'Start', 'Failed') as $type) {
                    foreach ($slot->getObjects($type) as $entity) {
                        if ($entity->getTime()>=$viewStart*60 and $entity->getTime() <= $viewEnd*60) {
                            $offset = ($type == 'Close') ? -2 : 0;
                            imagecopy(
                                $image,
                                $sprites[$type][$entity->getService()],
                                $this->getHPosByTime($entity->getTime()) + $offset,
                                $this->getVPosBySlotNo($i)-3,
                                0,
                                0,
                                4,
                                45
                            );
                        }
                    }
                }

            } elseif ($slot instanceof SlotContinuous) {
                for ($j = $borderLeft; $j <= $gridRight; $j++) {
                    imagecopy($image, $sprites['Mid'][$slot->getService()], $j, $this->getVPosBySlotNo($i)-3,0,0,1,45);
                }
                if ($slot->getDays() != 1) {
                $text='Online for '.$slot->getDays(). ' days';
                $textWidth = strlen($text)*imagefontwidth(2);
                imagefilledrectangle($image, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft-4, $borderTop+($i*$slotHeight)+10, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft+$textWidth+4, $borderTop+($i*$slotHeight)+25, $keyback);
                imagerectangle($image, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft-4, $borderTop+($i*$slotHeight)+10, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft+$textWidth+4, $borderTop+($i*$slotHeight)+25, $colourLine);
                imagestring($image, 2, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft, $borderTop+($i*$slotHeight)+10,  $text, $colourLine);
                }
            } elseif ($slot instanceof SlotGap and $slot->getDays() != 1) {
            $text='No connection for '.$slot->getDays(). ' days';
                $textWidth = strlen($text)*imagefontwidth(2);
                imagefilledrectangle($image, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft-5, $borderTop+($i*$slotHeight)+10, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft+$textWidth+4, $borderTop+($i*$slotHeight)+25, $keyback);
                imagerectangle($image, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft-4, $borderTop+($i*$slotHeight)+10, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft+$textWidth+4, $borderTop+($i*$slotHeight)+25, $colourLine);
                imagestring($image, 2, ($gridRight-$borderLeft-$textWidth)/2+$borderLeft, $borderTop+($i*$slotHeight)+10,  $text, $colourLine);
            }
        }

$dial = false; //FIXME: get from data somehow

        // Draw Footer

        $text="Visual Radius by Peter Smith (C) " . date("Y");
        $textWidth = strlen($text)*imagefontwidth(1);
        imagestring($image, 1, $imageWidth-$textWidth-5, $imageHeight-10,  $text, $colourLine);

        // Key
        $key_x=10;
        $key_y=$imageHeight-$borderBottom+10;

        imagefilledrectangle($image, $key_x, $key_y, $key_x+($dial ? 425 : 275), $key_y+50, $keyback);
        imagerectangle($image, $key_x, $key_y, $key_x+($dial ? 425 : 275), $key_y+50, $colourLine);

        imagestring($image, 4, $key_x+5, $key_y+15,  "Key:", $colourLine);

        imageline($image, $key_x+45, $key_y+40, $key_x+45, $key_y+10, $colourLine);

        imagecopy($image, $sprites['Mid']['broadband'], $key_x+57, $key_y+5,0,0,1,45);
        imagecopy($image, $sprites['Mid']['broadband'], $key_x+58, $key_y+5,0,0,1,45);
        imagecopy($image, $sprites['Start']['broadband'], $key_x+55, $key_y+5,0,0,4,45);
        imagestring($image, 2, $key_x+60, $key_y+10,  ":Session", $colourLine);
        imagestring($image, 2, $key_x+67, $key_y+25,  "Start", $colourLine);

        imageline($image, $key_x+115, $key_y+40, $key_x+115, $key_y+10, $colourLine);

        imagecopy($image, $sprites['Mid']['broadband'], $key_x+125, $key_y+2,0,0,1,45);
        imagecopy($image, $sprites['Mid']['broadband'], $key_x+126, $key_y+2,0,0,1,45);
        imagecopy($image, $sprites['Close']['broadband'], $key_x+125, $key_y+2,0,0,4,45);
        imagestring($image, 2, $key_x+130, $key_y+10,  ":Session", $colourLine);
        imagestring($image, 2, $key_x+137, $key_y+25,  "Close", $colourLine);

        imageline($image, $key_x+185, $key_y+40, $key_x+185, $key_y+10, $colourLine);

        imagecopy($image, $sprites['Failed']['broadband'], $key_x+195, $key_y+5,0,0,4,45);
        imagestring($image, 2, $key_x+200, $key_y+10,  ":Connection", $colourLine);
        imagestring($image, 2, $key_x+207, $key_y+25,  "Failed", $colourLine);


        // This end bit is only shown where there are dial-up connections.
        if ($dial) {
            imageline($image, $key_x+270, $key_y+40, $key_x+270, $key_y+10, $colourLine);

            for ($j=0; $j<7; $j++) {
                imagecopy($image, $sprites['Mid']['broadband'], $key_x+280+$j, $key_y+5,0,0,1,45);
            }
            imagestring($image, 2, $key_x+290, $key_y+18,  ":Broadband", $colourLine);

            imageline($image, $key_x+355, $key_y+40, $key_x+355, $key_y+10, $colourLine);

            for ($j=0; $j<7; $j++) {
                imagecopy($image, $sprites['Mid']['dialup'], $key_x+365+$j, $key_y+5,0,0,1,45);
            }
            imagestring($image, 2, $key_x+375, $key_y+18,  ":dial-up", $colourLine);
        }// End if
        return $image;
    }

    protected function getHPosByTime($time)
    {
        extract($this->options);

        return ((($time - ($viewStart * 60))
                 / ($viewLength * 60))
                 * $gridWidth) + $borderLeft;
    }

    protected function getVPosBySlotNo($slot)
    {
        return $this->options['borderTop']+($slot*$this->options['slotHeight']);
    }

    public static function makecolour($image, $colourString)
    {
        if (preg_match('/^#?([\da-f])([\da-f])([\da-f])$/i', $colourString, $matches)) {
            $r = hexdec($matches[1]) << 4;
            $g = hexdec($matches[2]) << 4;
            $b = hexdec($matches[3]) << 4;

        } elseif (preg_match('/^#?([\da-f]{2})([\da-f]{2})([\da-f]{2})$/i', $colourString, $matches)) {
            $r = hexdec($matches[1]);
            $g = hexdec($matches[2]);
            $b = hexdec($matches[3]);
        } else {
            throw new \InvalidArgumentException('Bad colour');
        }

        return imagecolorallocate($image, $r, $g, $b);
    }
}