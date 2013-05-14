<?php
namespace VisualRadius\Renderer;
use VisualRadius\Data\PreRenderedData;
interface RendererInterface
{
    public function __construct($app, array $options);
    public function render(PreRenderedData $data);
    public function getContentHeader();

}