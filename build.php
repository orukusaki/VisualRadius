<?php

$conf = array(
    'COMMIT_ID'       => getenv('COMMIT_ID'),
    'CI'              => getenv('CI'),
    'CI_BUILD_NUMBER' => getenv('CI_BUILD_NUMBER'),
    'CI_BUILD_URL'    => getenv('CI_BUILD_URL'),
    'CI_PULL_REQUEST' => getenv('CI_PULL_REQUEST'),
    'CI_BRANCH'       => getenv('CI_BRANCH'),
    'CI_NAME'         => getenv('CI_NAME'),
);
file_put_contents('build.json', json_encode($conf));
