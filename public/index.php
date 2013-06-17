<?php
// router for the inbuilt web server ** NOT FOR LIVE OK!! **
if (php_sapi_name() == 'cli-server' && preg_match('~assets~', $_SERVER["REQUEST_URI"])) {

    $absPath = __DIR__ . $_SERVER["REQUEST_URI"];

    if (is_file($absPath)) {

        $path = pathinfo($_SERVER["REQUEST_URI"]);

        $formats = array(
            'css' => 'text/css',
            'png' => 'image/png',
            'js'  => 'application/javascript',
        );

        if (array_key_exists($path['extension'], $formats)) {
            header('Content-Type: ' . $formats[$path['extension']]);
        }

        readfile($absPath);
        return true;
    }
    return false;
}

$baseDir = dirname(__DIR__);
require_once $baseDir . '/vendor/autoload.php';

$app = new Orukusaki\VisualRadius\Application(
    array(
        'base_dir' => $baseDir,
        'debug'    => getenv('DEBUG'),
    )
);

$app->run();
