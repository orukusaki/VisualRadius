<?php
/**
 * Contains the Gd class
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Renderer;

use VisualRadius\IRenderer;
use VisualRadius\Data\PreRenderedData;

/**
 * Renderer generates a png image using the gd library
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class Html implements IRenderer
{
    private $twig;

    public function __construct($app, array $options)
    {
        $this->twig = $app['twig'];
    }
    public function render(PreRenderedData $data, $imageId)
    {
        $content = $this->twig->render(
            'image.twig',
            array('imageId' => $imageId)
        );

        return function () use ($content) {
             echo $content;
        };
    }

    public function getContentHeader()
    {
        return array("Content-type" => "text/html");
    }

}