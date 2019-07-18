<?php
declare(strict_types=1);

require 'vendor/autoload.php';
header('Content-type:application/json;charset=utf-8');

use App\Tiles;
use App\Player;
use App\Game;
use App\Play;
use App\Outputter\StdOutput;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/play', 'App\Controllers\DominoController:play');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo 'Route not found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo 'Method not allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        list($controller, $action) = explode(":", $routeInfo[1], 3);

        $tiles     = new Tiles;
        $players[] = new Player('Alice', $tiles);
        $players[] = new Player('Bob', $tiles);
        $outputter = new StdOutput;

        (new $controller(
                    $tiles,
                    $players,
                    new Game($tiles, $players, $outputter),
                    new Play($tiles, $players[0], $outputter)
                )
            )->play();

        $outputter->output();
        break;
}
