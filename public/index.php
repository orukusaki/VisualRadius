<?php
// router for the inbuilt web server ** NOT FOR LIVE OK!! **
if (php_sapi_name() == 'cli-server' && preg_match('~assets~', $_SERVER["REQUEST_URI"])) {

    $absPath = __DIR__ . $_SERVER["REQUEST_URI"];

    if (is_file($absPath)) {

        $path = pathinfo($_SERVER["REQUEST_URI"]);

        switch ($path['extension']) {
            case 'css':
                header("Content-Type: text/css");
                break;
            case 'png':
                header("Content-Type: image/png");
                break;
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
