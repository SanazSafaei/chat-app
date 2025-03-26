<?php

declare(strict_types=1);

use App\Application\Handlers\RouteHandler;
use Slim\App;

return function (App $app) {
    (new RouteHandler())->getApplicationRouts($app);
};
