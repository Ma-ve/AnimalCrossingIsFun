<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Renderers;

use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpSpecializedException;
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Views\Twig;
use Throwable;

class ErrorRenderer implements ErrorRendererInterface {

    /**
     * @param HttpSpecializedException|Throwable $exception
     * @param bool                               $displayErrorDetails
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string {
        if($exception instanceof HttpSpecializedException) {
            $view = Twig::fromRequest($exception->getRequest());

            $message = $exception->getMessage();
            switch(get_class($exception)) {
                case HttpNotFoundException::class:
                    $message = 'This page doesn\'t exist!';
                    break;
            }

            return $view->fetch('pages/error/error.twig', [
                'error' => [
                    'code'    => $exception->getCode(),
                    'message' => $message,
                ],
            ]);
        }

        header("Location: /#error-by-redirect");
        exit;
    }
}