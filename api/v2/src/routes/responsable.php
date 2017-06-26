<?php
    $app->get('/hello1', function ($request, $response) {
        $data['auth'] = 'Hello Responsable 1';
            return echoRespnse(200, $response, $data);
    });
    $app->get('/hello2', function ($request, $response) {
        $data['auth'] = 'Hello Responsable 2';
            return echoRespnse(200, $response, $data);
    });