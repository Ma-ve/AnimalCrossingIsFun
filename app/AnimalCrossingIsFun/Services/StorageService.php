<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\User;
use Nyholm\Psr7\ServerRequest as Request;
use Nyholm\Psr7\Response;
use Throwable;

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
        try {
            $json = json_decode($request->getBody()->getContents(), true);

            $progressService = new ProgressService();
            $saveData = [];
            foreach($progressService->getAll()['all'] as $item) {
                $itemGroup = $item['group'];

                if(isset($json[$itemGroup]) && count($json[$itemGroup]) <= $item['count']) {
                    array_map(function($key, $value) {
                        if(mb_strlen($key) > 80 || !is_bool($value)) {
                            throw new Exception('Don\'t post illegal data');
                        }
                    }, array_keys($json[$itemGroup]), array_values($json[$itemGroup]));

                    $saveData[$itemGroup] = $json[$itemGroup];
                }
            }

            $this->cacheService->set($this->getCacheKey(), json_encode($saveData), null);
            $result = true;
        } catch(Throwable $throwable) {
            $result = false;
        }

        $response->getBody()->write(json_encode(['success' => $result]));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function loadFromDatabase(Response $response): Response {
        $data = false;

        $fromCache = $this->cacheService->get($this->getCacheKey());
        if(null !== $fromCache) {
            $data = json_decode($fromCache, true);
        }

        $response
            ->getBody()
            ->write(json_encode([
                'data' => $data,
            ]));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    private function getCacheKey(): string {
        return 'user.' . $this->user->getId();
    }

}
