<?php

namespace Orukusaki\VisualRadius;

use \Composer\Script\CommandEvent;

class Installer
{
    public static function postInstall(CommandEvent $event)
    {
        self::installAssets($event);
        self::gatherBuildInfo($event);
        \Sanpi\TwitterBootstrap\Composer\ScriptHandler::postInstall($event);
    }

    public static function postUpdate(CommandEvent $event)
    {
        self::installAssets($event);
        self::gatherBuildInfo($event);
        \Sanpi\TwitterBootstrap\Composer\ScriptHandler::postUpdate($event);
    }

    static private function installAssets(CommandEvent $event)
    {
        $event->getIO()->write('<info>Installing assets</info>');
        $options = $event->getComposer()->getPackage()->getExtra();
        $webDir = $options['symfony-web-dir'];

        if (!is_dir($webDir)) {
            mkdir($webDir);
        }

        $resourcesFolders = $options['resources'];

        foreach ($resourcesFolders as $folder => $dest) {

            $destFolder = "$webDir/$dest/";

            if (!is_dir($destFolder)) {
                mkdir($destFolder);
            }

            foreach (glob("$folder/*") as $file) {

                $dest = $destFolder . basename($file);

                $event->getIO()->write("Copying $file => $dest");
                copy($file, $dest);
            }
        }
    }

    private static function gatherBuildInfo(CommandEvent $event)
    {
        $conf = array(
            'COMMIT_ID'       => getenv('COMMIT_ID'),
            'CI'              => getenv('CI'),
            'CI_BUILD_NUMBER' => getenv('CI_BUILD_NUMBER'),
            'CI_BUILD_URL'    => getenv('CI_BUILD_URL'),
            'CI_PULL_REQUEST' => getenv('CI_PULL_REQUEST'),
            'CI_BRANCH'       => getenv('CI_BRANCH'),
            'CI_NAME'         => getenv('CI_NAME'),
            'CI_BUILD_TIME'   => date('c'),
        );
        file_put_contents('build.json', json_encode($conf));
    }

}
