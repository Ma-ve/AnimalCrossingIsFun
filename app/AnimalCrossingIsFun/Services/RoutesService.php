<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\RecipeCategory as RecipeCategoryDto;
use Mave\AnimalCrossingIsFun\Dto\Dto;
use Mave\AnimalCrossingIsFun\Dto\Language as LanguageDto;
use Mave\AnimalCrossingIsFun\OAuth\LoginProvider;
use Mave\AnimalCrossingIsFun\OAuth\RedditProvider;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\RecipeCategoryRepository;
use Mave\AnimalCrossingIsFun\Repositories\LanguageRepository;
use Mave\AnimalCrossingIsFun\Repositories\TranslationsRepository;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;

class RoutesService {

    /**
     * @var App
     */
    private $app;

    /**
     * @param App $app
     */
    public function __construct(App $app) {
        $this->app = $app;
    }

    /** @noinspection PhpUnusedParameterInspection */
    /**
     * @return $this
     */
    public function registerProfileRoutes(): self {
        $this->app->group('/profile', function(RouteCollectorProxy $collectorProxy) {
            $collectorProxy->redirect('', '/profile/');

            $collectorProxy->group('/api', function(RouteCollectorProxy $collectorProxy) {
                $collectorProxy->post('/save', function(Request $request, Response $response) {
                    return (new StorageService(UserService::getUser(false)))
                        ->saveToDatabase($request, $response);
                });

                $collectorProxy->post('/load', function(Request $request, Response $response) {
                    return (new StorageService(UserService::getUser(false)))
                        ->loadFromDatabase($response);
                });
            });

            $collectorProxy->get('/', function(Request $request, Response $response) {
                $view = Twig::fromRequest($request);

                return $view->render($response, 'pages/profile.twig', [
                    'progressItems' => (new ProgressService())->getAll(),
                ]);
            });
        })
            ->add(function(Request $request, RequestHandlerInterface $requestHandler) {
                session_start();

                if(empty($_SESSION)) {
                    header("Location: /");
                    exit;
                }

                return $requestHandler->handle($request);
            });

        return $this;
    }

    /** @noinspection PhpUnusedParameterInspection */
    /**
     * @return $this
     */
    public function registerRecipesRoutes(): self {
        $this->app->group('/recipes', function(RouteCollectorProxy $collectorProxy) {
            $collectorProxy->get('/{category}', function(Request $request, Response $response, $args) {
                $view = Twig::fromRequest($request);

                $recipeCategories = $this->getRecipeCategoriesForCategory($args['category']);
                if(!$recipeCategories) {
                    return $this->categoryNotFound($view, $response);
                }

                $recipeCategory = $recipeCategories[0];

                $repositoryRepository = $recipeCategory->getRecipeRepository()
                    ->loadAll()
                    ->sortItems($sort = ($request->getQueryParams()['sort'] ?? false))
                    ->loadFiltersIntoData();

                return $view->render($response, 'pages/recipes/category.twig', [
                    'items'            => $repositoryRepository->getAll(),
                    'filters'          => $repositoryRepository->getFilters(),
                    'recipeCategories' => $recipeCategories,
                    'sort'             => $sort,
                ]);
            });

            $collectorProxy->get('/{category}/{recipe}', function(Request $request, Response $response, $args) {
                $view = Twig::fromRequest($request);

                $recipeCategories = $this->getRecipeCategoriesForCategory($args['category']);
                if(!$recipeCategories) {
                    return $this->categoryNotFound($view, $response);
                }

                $recipeCategory = $recipeCategories[0];

                $item = $recipeCategory->getRecipeRepository()
                    ->loadAll()
                    ->get($args['recipe']);

                if(false === $item) {
                    return $view->render($response, 'pages/error/error.twig', [
                        'error' => [
                            'code'    => 404,
                            'message' => 'Recipe Not Found',
                        ],
                    ]);
                }

                return $view->render($response, 'pages/recipes/detail.twig', [
                    'item'           => $item,
                    'recipeCategory' => $recipeCategory,
                ]);
            });

        });

        return $this;
    }

    /**
     * @param string $category
     *
     * @return RecipeCategoryDto[]|Dto[]
     * @throws Exception
     */
    private function getRecipeCategoriesForCategory(string $category) {
        return (new RecipeCategoryRepository(null))
            ->loadAll()
            ->getMultipleBySingleKey($category);
    }

    /**
     * @param Twig     $view
     * @param Response $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function categoryNotFound(Twig $view, Response $response) {
        return $view->render($response, 'pages/error/error.twig', [
            'error' => [
                'code'    => 404,
                'message' => 'Recipe Category Not Found',
            ],
        ]);
    }

    /** @noinspection PhpUnusedParameterInspection */
    /**
     * @return $this
     */
    public function registerAuthRoutes(): self {
        $this->app->group('/auth', function(RouteCollectorProxy $collectorProxy) {
            $collectorProxy->get('/me', function(Request $request, Response $response) {
                $data = false;

                $getUser = function() {
                    $user = user();
                    if(!$user) {
                        (new LoginProvider())
                            ->restoreUserFromLoginCookie();
                        $user = user(true);
                    }

                    return $user;
                };
                $user = $getUser();

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
                (new CookieService())
                    ->setLoginCookie(null);
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
            ->add(function(Request $request, RequestHandlerInterface $requestHandler) {
                session_start();

                return $requestHandler->handle($request);
            });

        return $this;
    }

    /** @noinspection PhpUnusedParameterInspection */
    /**
     * @return $this
     */
    public function registerTranslationsRoutes(): self {
        $this->app->group('/translations', function(RouteCollectorProxy $collectorProxy) {
            $collectorProxy->get('/load/{language}', function(Request $request, Response $response, $args) {
                $languageRepository = new LanguageRepository(null);

                /** @var LanguageDto $language */
                $language = $languageRepository->loadAll()->get($args['language']);
                if(!$language) {
                    return $this->returnJson($response, ['errors' => 'Invalid data']);
                }

                $cacheKey = "translations.{$language->getLangCode()}";
                $cacheService = new CacheService();
                $cachedData = $cacheService->get($cacheKey);
                if(null !== $cachedData) {
                    return $this->returnJson($response, ['data' => json_decode($cachedData, true)]);
                }

                $data = (new TranslationsRepository(null))
                    ->loadAll()
                    ->get($language->getLangCode());

                $cacheService->set($cacheKey, json_encode($data), 60 * 60);

                return $this->returnJson($response, [
                    'data' => $data,
                ]);
            });

            $collectorProxy->post('/suggest', function(Request $request, Response $response) {
                $json = json_decode($request->getBody()->getContents(), true);

                if(
                    !$json ||
                    json_last_error() !== JSON_ERROR_NONE ||
                    !is_array($json) ||
                    !isset($json['key']) ||
                    !isset($json['translation']) ||
                    !isset($json['langCode'])
                ) {
                    return $this->returnJson($response, ['errors' => 'Invalid data']);
                }

                $key = $json['key'];
                $suggestion = $json['translation'];
                $langCode = $json['langCode'];

                if(!is_string($key) || strlen($key) > 40) {
                    throw new Exception("Invalid key: '{$key}'");
                }
                if(!is_string($langCode) || strlen($langCode) > 8) {
                    throw new Exception("Invalid language: '{$langCode}'");
                }
                if(!is_string($suggestion) || strlen($suggestion) > 40) {
                    throw new Exception("Invalid suggestion: '{$suggestion}'");
                }

                $uq = uniqid();

                $cacheService = new CacheService();
                $cacheService->set("suggestion.{$langCode}.{$key}.{$uq}", $suggestion, 60 * 60 * 24 * 180);

                return $this->returnJson($response, [
                    'data' => true,
                ]);
            });
        });

        return $this;
    }

    /**
     * @param Response $response
     * @param array    $data
     *
     * @return Response
     */
    private function returnJson(Response $response, array $data) {
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

}
