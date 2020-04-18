<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Mave\AnimalCrossingIsFun\Dto\User;
use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;

class StorageService {

    /**
     * @var User
     */
    private $user;

    /**
     * @var CacheService
     */
    private $cacheService;

    /**
     * @param User $user
     */
    public function __construct(User $user) {
        $this->user = $user;
        $this->cacheService = new CacheService();
    }

    public function saveToDatabase(Request $request, Response $response): Response {
    }

    public function loadFromDatabase(Request $request, Response $response): Response {
    }

}
