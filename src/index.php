<?php

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Mave\AnimalCrossingIsFun\Repositories\BugsRepository;
use Mave\AnimalCrossingIsFun\Repositories\FishRepository;
use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

define('BASE_PATH', __DIR__ . '/../');
require(BASE_PATH . 'vendor/autoload.php');

$adapters = [
    new EnvConstAdapter(),
    new ServerConstAdapter(),
];

$repository = RepositoryBuilder::create()
    ->withReaders($adapters)
    ->withWriters($adapters)
    ->immutable()
    ->make();

Dotenv::create($repository, BASE_PATH, null)->load();

if(env('IS_DEV', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

try {

    $app = AppFactory::create();

    $app->add(TwigMiddleware::create($app, Twig::create(BASE_PATH . 'views/', [
        'cache' => BASE_PATH . 'cache/',
    ])));

    $app->addErrorMiddleware(env('IS_DEV', false), env('IS_DEV', false), env('IS_DEV', false));



// Add routes
    $app->get('/fish', function(Request $request, Response $response) {
        $repository = (new FishRepository(null))
            ->loadAll()
            ->sortItems($request->getAttributes()['sort'] ?? false);

        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/fish.twig', [
            'items' => $repository->getAll(),
        ]);
    });

    $app->get('/bugs', function(Request $request, Response $response) {
        $repository = (new BugsRepository(null))
            ->loadAll()
            ->sortItems($request->getAttributes()['sort'] ?? false);

        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/bugs.twig', [
            'items' => $repository->getAll(),
        ]);
    });

    $app->run();

    return;
} catch(Throwable $throwable) {
}