<?php
namespace VisualRadius\Decorator;

use VisualRadius\Data\PreRenderedData;

interface DecoratorInterface
{
    public function decorate(PreRenderedData $data);
}
