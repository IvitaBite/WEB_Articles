<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Response;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader(__DIR__ . '/app/Views');
$twig = new Environment($loader);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $router) {
    $router->addRoute('GET', '/articles', ['App\Controllers\ArticleController', 'index']);
    $router->addRoute('GET', '/articles/{type}', ['App\Controllers\ArticleController', 'show']);
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
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:

        $vars = $routeInfo[2];

        [$controller, $method] = $routeInfo[1];

        $response = (new $controller)->{$method}($vars);

        if ($response instanceof Response) {
            $viewName = $response->getViewName();
            $data = $response->getData();

            echo $twig->render($viewName . '.twig', $data);
        }
        break;
}