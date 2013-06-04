<?php
namespace Orukusaki\VisualRadius\Decorator;

use Orukusaki\VisualRadius\Data\PreRenderedData;

interface DecoratorInterface
{
    public function decorate(PreRenderedData $data);
}
