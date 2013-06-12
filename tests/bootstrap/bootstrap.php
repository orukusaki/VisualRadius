<?php
$loader = require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$loader->add('Orukusaki\\VisualRadius\\Test', dirname(__DIR__));
$loader->add('Orukusaki\\VisualRadius\\BehatContext', dirname(__DIR__));