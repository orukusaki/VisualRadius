<?php
namespace VisualRadius;
use VisualRadius\Data\PreRenderedData;
interface IRenderer
{
    public function __construct($app, array $options);
    public function render(PreRenderedData $data, $imageId);
    public function getContentHeader();

}