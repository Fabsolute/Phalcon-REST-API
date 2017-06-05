<?php
$loader = include_once '../vendor/autoload.php';

$di = new \Fabs\Rest\DI();

$di->setShared('cache', function () use ($di) {

    $frontend = new Phalcon\Cache\Frontend\Data([
        'lifetime' => 3600 // 6 hours
    ]);
    $redis = new \Phalcon\Cache\Backend\Redis($frontend,
        [
        ]
    );
    return $redis;
});

$di->api_handler->registerFolder('../api');

$di->application->handle();