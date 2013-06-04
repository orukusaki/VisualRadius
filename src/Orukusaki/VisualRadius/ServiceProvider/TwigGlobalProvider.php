<?php

namespace Orukusaki\VisualRadius\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class TwigGlobalProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        if (!$app->offsetExists('twig')) {
            throw new Exception('Twig needs to be loaded first');
        }

        $twig = $app['twig'];

        $buildData = json_decode(file_get_contents($app['build.file']), true);
        $twig->addGlobal('build', $buildData);
    }
}
