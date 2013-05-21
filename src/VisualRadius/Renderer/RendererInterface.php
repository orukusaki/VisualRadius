<?php
/**
 * Contains RendererInterface
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Renderer;

use VisualRadius\Data\PreRenderedData;

/**
 * Defines a renderer
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
interface RendererInterface
{
    /**
     * Constructor
     *
     * @param ArrayAccess $container Container
     *
     * @return void
     */
    public function __construct(\ArrayAccess $container);

    /**
     * Render
     *
     * @param PreRenderedData $object Data to render
     *
     * @return Closure
     */
    public function render(PreRenderedData $object);

    /**
     * Get Content Header
     *
     * @return array
     */
    public function getContentHeader();
}
