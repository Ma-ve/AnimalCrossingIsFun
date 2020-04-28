<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Mave\AnimalCrossingIsFun\OAuth\LoginProvider;
use Mave\AnimalCrossingIsFun\OAuth\RedditProvider;
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
                    return (new StorageService(UserService::getUser()))
                        ->saveToDatabase($request, $response);
                });

                $collectorProxy->post('/load', function(Request $request, Response $response) {
                    return (new StorageService(UserService::getUser()))
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

}
