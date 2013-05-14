<?php
/**
 * Contains the Gd class
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Renderer;

use VisualRadius\Data\PreRenderedData;

/**
 * Renderer generates a png image using the gd library
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class Html implements RendererInterface
{
    private $twig;

    public function __construct($app, array $options)
    {
        $this->twig = $app['twig'];
    }
    public function render(PreRenderedData $data)
    {
        $content = $this->twig->render(
            'image.twig',
            array('imageId' => $data->getId())
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