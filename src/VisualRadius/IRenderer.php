<?php
namespace VisualRadius;
use VisualRadius\Data\PreRenderedData;
interface IRenderer
{
    public function __construct(array $options);
    public function render(PreRenderedData $data, $imageId);
    public function getContentHeader();
    public function getStream();

}