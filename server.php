<?php

require __DIR__ . '/vendor/autoload.php';

$http = new swoole_http_server("127.0.0.1", 9501);

$http->on('request', function ($request, $response) {
    print_r($request);
    // print_r($response);
    print_r(\One\Protocol\Factory::newRequest($request));

    $response->end("EOF\n");
});

$http->start();
