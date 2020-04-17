<?php

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Mave\AnimalCrossingIsFun\OAuth\RedditProvider;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\CherryBlossomRecipeRepository;
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

    $routesRepository = new RoutesRepository();;
    $twig->getEnvironment()->addGlobal('routesRepository', $routesRepository);

    $app->add(TwigMiddleware::create($app, $twig));

    $app->addErrorMiddleware(env('IS_DEV', false), env('IS_DEV', false), env('IS_DEV', false));


    $app->get('/', function(Request $request, Response $response) {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/home.twig');
    })
        ->setName('/');

    $app->group('/profile', function(Slim\Routing\RouteCollectorProxy $collectorProxy) {
        $collectorProxy->redirect('', '/profile/');

        $collectorProxy->get('/', function(Request $request, Response $response) {
            $view = Twig::fromRequest($request);

            return $view->render($response, 'pages/profile.twig');
        });
    })
        ->add(function(Request $request, Psr\Http\Server\RequestHandlerInterface $requestHandler) {
            session_start();

            if(empty($_SESSION)) {
                header("Location: /");
                exit;
            }

            return $requestHandler->handle($request);
        });

    $app->group('/auth', function(Slim\Routing\RouteCollectorProxy $collectorProxy) {
        $collectorProxy->get('/me', function(Request $request, Response $response) {
            $user = user();
            $data = false;
            if($user) {
                $data = [
                    'name' => $user->getUsername(),
                ];
            }

            $response
                ->getBody()
                ->write(json_encode([
                    'data' => $data,
                ]));

            return $response->withHeader('Content-Type', 'application/json');
        });

        $collectorProxy->get('/logout', function() {
            $_SESSION = [];
            session_destroy();
            header("Location: /");
            exit;
        });

        $collectorProxy->get('/reddit/login', function() {
            (new RedditProvider())
                ->start();
        });

        $collectorProxy->get('/reddit/callback', function(Request $request, Response $response) {
            (new RedditProvider())
                ->handleCallback($request);

            header("Location: /");
            exit;
        });
    })
        ->add(function(Request $request, Psr\Http\Server\RequestHandlerInterface $requestHandler) {
            session_start();

            return $requestHandler->handle($request);
        });;


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
        })
            ->setName($route->getUrl());
    }

    $app->get('/recipes/{category}/{recipe}', function(Request $request, Response $response, $args) {
        $item = (new CherryBlossomRecipeRepository(null))
            ->loadAll()
            ->get($args['recipe']);

        $view = Twig::fromRequest($request);

        if(false === $item) {
            return $view->render($response, '404');
        }

        return $view->render($response, 'pages/detail/recipe.twig', [
            'item' => $item,
        ]);
    });


    $app->run();

    return;
} catch(Throwable $throwable) {
}