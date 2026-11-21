<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Predis\Client;
use Predis\ClientInterface;

class CacheService {

    /**
     * @var string
     */
    private $keyPrefix;

    public function __construct() {
        $this->keyPrefix = env('IS_DEV', false) ? 'd.' : 'p.';
    }

    /**
     * @var ClientInterface|null
     */
    private static $client;

    private function client(): ClientInterface {
        self::$client ??= new Client([
            'scheme'  => 'tcp',
            'host'    => env('REDIS_HOST', '127.0.0.1'),
            'port'    => (int)env('REDIS_PORT', 6379),
            'timeout' => 1,
        ]);

        return self::$client;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string {
        return $this->client()->get($this->keyPrefix . $key);
    }

    /**
     * @param array $keys
     *
     * @return array<string|null>
     */
    public function getMultiple(array $keys): ?array {
        return $this->client()->mget(array_map(fn($key) => $this->keyPrefix . $key, $keys));
    }

    /**
     * @param string   $key
     * @param string   $data
     * @param int|null $seconds
     */
    public function set(string $key, string $data, ?int $seconds) {
        if(null === $seconds) {
            $this->client()->set($this->keyPrefix . $key, $data);

            return;
        }

        $this->client()->set($this->keyPrefix . $key, $data, 'EX', $seconds);
    }

    /**
     * @param string $key
     */
    public function delete(string $key) {
        $this->client()->del($this->keyPrefix . $key);
    }

}
