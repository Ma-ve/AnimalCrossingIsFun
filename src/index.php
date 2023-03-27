<?php

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Mave\AnimalCrossingIsFun\Dto\Language as LanguageDto;
use Mave\AnimalCrossingIsFun\Renderers\ErrorRenderer;
use Mave\AnimalCrossingIsFun\Repositories\EventRepository;
use Mave\AnimalCrossingIsFun\Repositories\LanguageRepository;
use Mave\AnimalCrossingIsFun\Repositories\RoutesRepository;
use Mave\AnimalCrossingIsFun\Repositories\VillagerRepository;
use Mave\AnimalCrossingIsFun\Services\ProgressService;
use Mave\AnimalCrossingIsFun\Services\RoutesService;
use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
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

        $villagers = (new VillagerRepository(null))
            ->loadAll()
            ->getByNearbyBirthdates(new DateTime());
        $villagers[] = $villagers[0];

        $firstVillagers = array_slice($villagers, 0, ceil(count($villagers) / 2));
        $secondVillagers = array_slice($villagers, ceil(count($villagers) / 2), floor(count($villagers) / 2));
        $columnedVillagers = [];
        foreach($firstVillagers as $index => $villager) {
            $columnedVillagers[$index][] = $villager;
        }
        foreach($secondVillagers as $index => $villager) {
            $columnedVillagers[$index][] = $villager;
        }

        return $view->render($response, 'pages/home.twig', [
            'progressItems' => (new ProgressService())->getAll(),
            'villagers'     => array_merge(...$columnedVillagers),
            'events'        => (new EventRepository(null))
                ->loadAll()
                ->getByNearbyStartDates(new DateTime()),
        ]);
    })
        ->setName('/');

    (new RoutesService($app))
        ->registerRecipesRoutes()
        ->registerSettingsRoutes()
        ->registerAuthRoutes()
        ->registerTranslationsRoutes();

    $app->group('/profile', function(RouteCollectorProxy $collectorProxy) {
        $collectorProxy->redirect('', '/settings/');
        $collectorProxy->get('/', function(Request $request, Response $response) {
            return $response->withStatus(302)->withHeader('Location', '/settings');
        });
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


    $app->run();

    return;
} catch(Throwable $throwable) {
    throw $throwable;
}