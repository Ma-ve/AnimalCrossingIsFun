<?php

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Mave\AnimalCrossingIsFun\Dto\Language as LanguageDto;
use Mave\AnimalCrossingIsFun\OAuth\RedditProvider;
use Mave\AnimalCrossingIsFun\Renderers\ErrorRenderer;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\CherryBlossomRecipeRepository;
use Mave\AnimalCrossingIsFun\Repositories\EventRepository;
use Mave\AnimalCrossingIsFun\Repositories\LanguageRepository;
use Mave\AnimalCrossingIsFun\Repositories\RoutesRepository;
use Mave\AnimalCrossingIsFun\Repositories\VillagerRepository;
use Mave\AnimalCrossingIsFun\Services\CacheService;
use Mave\AnimalCrossingIsFun\Services\ProgressService;
use Mave\AnimalCrossingIsFun\Services\RoutesService;
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

define('ROUTES_PATH', BASE_PATH . 'data/routes/');

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

    $errorMiddleware = $app->addErrorMiddleware(
        env('IS_DEV', false),
        !env('IS_DEV', true),
        !env('IS_DEV', true)
    );
    $errorMiddleware
        ->getDefaultErrorHandler()
        ->registerErrorRenderer('text/html', ErrorRenderer::class);


    $app->get('/', function(Request $request, Response $response) {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/home.twig', [
            'progressItems' => (new ProgressService())->getAll(),
            'villagers'     => (new VillagerRepository(null))
                ->loadAll()
                ->getByNearbyBirthdates(new DateTime()),
            'events'        => (new EventRepository(null))
                ->loadAll()
                ->getByNearbyStartDates(new DateTime()),
        ]);
    })
        ->setName('/');

    (new RoutesService($app))
        ->registerProfileRoutes()
        ->registerAuthRoutes();

    $app->post('/translations/suggest', function(Request $request, Response $response) {
        $json = json_decode($request->getBody()->getContents(), true);

        $return = function(array $data) use ($response) {
            $response->getBody()->write(json_encode($data));

            return $response
                ->withHeader('Content-Type', 'application/json');
        };

        if(
            !$json ||
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($json) ||
            !isset($json['key']) ||
            !isset($json['translation']) ||
            !isset($json['langCode'])
        ) {
            return $return(['errors' => 'Invalid data']);
        }

        $key = $json['key'];
        $suggestion = $json['translation'];
        $langCode = $json['langCode'];

        if(!is_string($key) || $key > 40) {
            throw new Exception("Invalid key: '{$key}'");
        }
        if(!is_string($langCode) || $langCode > 8) {
            throw new Exception("Invalid language: '{$langCode}'");
        }
        if(!is_string($suggestion) || $suggestion > 40) {
            throw new Exception("Invalid suggestion: '{$suggestion}'");
        }

        $uq = uniqid();

        $cacheService = new CacheService();
        $cacheService->set("suggestion.{$langCode}.{$key}.{$uq}", $suggestion, 60 * 60 * 24 * 180);

        return $return([
            'data' => true,
        ]);
    });

    $app->get('/settings', function(Request $request, Response $response) {
        $view = Twig::fromRequest($request);

        /** @var LanguageDto[] $languages */
        $languages = (new LanguageRepository(null))
            ->loadAll()
            ->getAll();

        return $view->render($response, 'pages/settings.twig', [
            'languages' => $languages,
        ]);
    });

    $routesRegistered = [];
    foreach($routesRepository->getAll() as $menuItem) {
        foreach($menuItem->getRoutes() as $route) {
            if(isset($routesRegistered[$route->getUrl()])) {
                continue;
            }
            $routesRegistered[$route->getUrl()] = true;
            $app->get($route->getUrl(), function(Request $request, Response $response) use ($route) {
                $repository = $route->getRepository()
                    ->loadAll()
                    ->sortItems($sort = ($request->getQueryParams()['sort'] ?? false))
                    ->loadFiltersIntoData();

                $view = Twig::fromRequest($request);

                return $view->render($response, $route->getTwigView(), [
                    'items'   => $repository->getAll(),
                    'filters' => $repository->getFilters(),
                    'sort'    => $sort,
                ]);
            })
                ->setName($route->getUrl());
        }
    }

    $app->get('/events/{event}', function(Request $request, Response $response, $args) {
        $events = (new EventRepository(null))
            ->loadAll()
            ->sortItems()
            ->getMultipleBySingleKey($args['event']);

        $view = Twig::fromRequest($request);

        if(empty($events)) {
            return $view->render($response, 'pages/error/error.twig', [
                'error' => [
                    'code'    => 404,
                    'message' => 'Event Not Found!',
                ],
            ]);
        }

        return $view->render($response, 'pages/detail/event.twig', [
            'events' => $events,
        ]);
    });

    $app->redirect('/recipes', '/recipes/cherry-blossom-season');

    $app->get('/recipes/{category}/{recipe}', function(Request $request, Response $response, $args) {
        $item = (new CherryBlossomRecipeRepository(null))
            ->loadAll()
            ->get($args['recipe']);

        $view = Twig::fromRequest($request);

        if(false === $item) {
            return $view->render($response, 'pages/error/error.twig', [
                'error' => [
                    'code'    => 404,
                    'message' => 'Recipe Not Found',
                ],
            ]);
        }

        return $view->render($response, 'pages/detail/recipe.twig', [
            'item' => $item,
        ]);
    });


    $app->run();

    return;
} catch(Throwable $throwable) {
}