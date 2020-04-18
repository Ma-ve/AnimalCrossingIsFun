<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use RedisClient\Client\Version\RedisClient5x0;
use RedisClient\ClientFactory;
use RedisClient\RedisClient;

class CacheService {

    /**
     * @var string
     */
    private $keyPrefix;

    public function __construct() {
        $this->keyPrefix = env('IS_DEV', false) ? 'd.' : 'p.';
    }

    /**
     * @var RedisClient5x0
     */
    private static $client;

    private function client(): RedisClient {
        if(null === self::$client) {
            self::$client = ClientFactory::create([
                'server'  => '127.0.0.1:6379',
                'timeout' => 1,
                'version' => '5.0.8',
            ]);
        }

        return self::$client;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string {
        return self::client()->get($this->keyPrefix . $key);
    }

    /**
     * @param array $keys
     *
     * @return string|string[]
     */
    public function getMultiple(array $keys): ?array {
        return self::client()->mget(array_map(function($key) {
            return $this->keyPrefix . $key;
        }, $keys));
    }

    /**
     * @param string $key
     * @param string $data
     * @param int    $seconds
     */
    public function set(string $key, string $data, ?int $seconds) {
        self::client()->set($this->keyPrefix . $key, $data, $seconds);
    }

}
