<?php
namespace VisualRadius;

use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use FOS\Rest\Util\FormatNegotiator;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Silex\ServiceProvider\DoctrineMongoDBServiceProvider;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VisualRadius\ServiceProvider\TwigGlobalProvider;

require_once __DIR__. '/../vendor/autoload.php';

$app = new Application;
$app['debug'] = getenv('DEBUG');
$app['negotiator'] = new FormatNegotiator;
$app->register(new UrlGeneratorServiceProvider);
$app->register(new ConfigServiceProvider(__DIR__ . '/../config.json'));
$app->register(new TwigServiceProvider, array('twig.path' => __DIR__ . '/../templates'));
$app->register(new TwigGlobalProvider, array('build.file' => __DIR__ . '/../build.json'));

// Why isn't this done in the service provider?
AnnotationDriver::registerAnnotationClasses();

$app->register(
    new DoctrineMongoDBServiceProvider,
    array(
        'doctrine.odm.mongodb.connection_options' => array(
            // 'database' => 'my_database_name', //TODO: Get config
            'host'     => 'localhost',
        ),
        'doctrine.odm.mongodb.documents' => array(
            array(
                'type' => 'annotation',
                'path' => '/src/VisualRadius/Data',
                'namespace' => 'VisualRadius\\Data'
            ),
        ),
        'doctrine.odm.mongodb.proxies_dir'             => __DIR__ . '/../cache',
        'doctrine.odm.mongodb.proxies_namespace'       => 'DoctrineMongoDBProxy',
        'doctrine.odm.mongodb.auto_generate_proxies'   => true,
        'doctrine.odm.mongodb.hydrators_dir'           => __DIR__ . '/../cache',
        'doctrine.odm.mongodb.hydrators_namespace'     => 'DoctrineMongoDBHydrator',
        'doctrine.odm.mongodb.auto_generate_hydrators' => true,
        'doctrine.odm.mongodb.metadata_cache'          => 'ArrayCache',
    )
);
// ^^ All this is Bootstrap


//============================================================================================


// \/\/ All this is the actual controller code.

$app->before(
    function (Request $request) use ($app) {
        $app['format'] = $app['negotiator']->getBestFormat($request, array_keys($app['formats']));
    }
);

$app->after(
    function (Request $request, Response $response) use ($app) {
        if ($response->isSuccessful()) {
            $app['doctrine.odm.mongodb.dm']->flush();
        }
    }
);

$app->get(
    '/image/{imageId}.{format}',
    function (Request $request, $imageId, $format) use ($app) {

        // If format is not specified in the url, or is something unknown, use the negotiated format from
        // the Accept header.
        if (is_null($format) || ! array_key_exists($format, $app['formats'])) {
            $format = $app['format'];
        }

        $options = array_merge($app['image'], $app['cache']);
        $renderer = new $app['formats'][$format]($app, $options);

        $preRenderedData = $app['doctrine.odm.mongodb.dm']->find('VisualRadius\\Data\\PreRenderedData', $imageId);

        // if (!$preRenderedData) {
        //      //TODO: Impliment
        //     return new Response(
        //         $renderer->getNotFoundContent($imageId),
        //         404
        //     );
        // }

        $preRenderedData->updateLastAccess();

        return $app->stream(
            $renderer->render($preRenderedData),
            200,
            $renderer->getContentHeader()
        );
    }
)
->value('format', null)
->assert('imageId', '\w{24}')
->bind('viewImage');

$app->post(
    '/image',
    function (Request $request) use ($app) {

        $options = array_merge($app['image'], $app['cache']);

        $records = new DataSource\PastedRecords($request->get('pastedRecords'));
        $preRenderedData = Data\PreRenderedData::buildFromSessionData($records->getData(), $options);

        foreach ($app['decorators'] as $name => $class) {

            if ($request->get('decorator-' . $name)) {
                $decorator = new $class();
                $decorator->decorate($preRenderedData);
            }
        }

        if ($request->get('save')) {
            $app['doctrine.odm.mongodb.dm']->persist($preRenderedData);
            $app['doctrine.odm.mongodb.dm']->flush();
            return $app->redirect(
                $app['url_generator']->generate('viewImage', array('imageId' => $preRenderedData->getId())),
                302
            );
        }

        // Full screen rendering of an image directly to .png format
        if ($app['format'] == 'html') {
            $app['format'] = 'png';
        }

        $renderer = new $app['formats'][$app['format']]($app, $options);

        return $app->stream(
            $renderer->render($preRenderedData),
            200,
            $renderer->getContentHeader()
        );
    }
)->bind('generate');

$app->get(
    '/',
    function () use ($app) {
        return $app['twig']->render(
            'index.twig'
        );
    }
)->bind('home');

$app->get(
    '/api',
    function () use ($app) {
        return $app['twig']->render(
            'api.twig'
        );
    }
)->bind('api');

$app->run();
