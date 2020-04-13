<?php

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Mave\AnimalCrossingIsFun\Repositories\RoutesRepository;
use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;

define('BASE_PATH', __DIR__ . '/../');
require(BASE_PATH . 'vendor/autoload.php');

$adapters = [
    new EnvConstAdapter(),
    new ServerConstAdapter(),
];

Dotenv::createImmutable(BASE_PATH)
    ->load();


if(env('IS_DEV', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

try {

    $app = AppFactory::create();

    $twig = Twig::create(BASE_PATH . 'views/', [
        'cache' => BASE_PATH . 'cache/',
        'debug' => env('IS_DEV', false),
    ]);
    if(env('IS_DEV')) {
        $twig->addExtension(new DebugExtension());
    }

    $app->add(TwigMiddleware::create($app, $twig));

    $app->addErrorMiddleware(env('IS_DEV', false), env('IS_DEV', false), env('IS_DEV', false));


    $routesRepository = new RoutesRepository();
    foreach($routesRepository->getAll() as $route) {
        $app->get($route->getUrl(), function(Request $request, Response $response) use($route) {
            $repository = $route->getRepository()
                ->loadAll()
                ->sortItems($sort = ($request->getQueryParams()['sort'] ?? false));

            $view = Twig::fromRequest($request);

            return $view->render($response, $route->getTwigView(), [
                'items' => $repository->getAll(),
                'sort'  => $sort,
            ]);
        });
    }

    $app->run();

    return;
} catch(Throwable $throwable) {
}