<?php
$baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
require_once $baseDir . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$app = new Orukusaki\VisualRadius\Application(
    array(
        'base_dir' => $baseDir,
        'debug'    => getenv('DEBUG'),
    )
);

$app->run();
