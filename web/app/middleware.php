<?php
declare(strict_types=1);

use \App\Application\Middleware as Middlewares;
use Slim\App;

return function (App $app) {
    $app->add(Middlewares\SessionMiddleware::class);
    $app->add(Middlewares\JsonBodyParserMiddleware::class);
};
