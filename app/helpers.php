<?php

use Mave\AnimalCrossingIsFun\Dto\User;
use Mave\AnimalCrossingIsFun\Services\UserService;

if(!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function env($key, $default = null) {
        $value = getenv($key);

        if($value === false) {
            return value($default);
        }

        switch(strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return null;
        }

        if(
            strlen($value) > 1 &&
            substr($value, 0, strlen('"')) === (string)'"' &&
            substr($value, -strlen('"')) === (string)'"'
        ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if(!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value) {
        return $value instanceof Closure ? $value() : $value;
    }
}

if(!function_exists('user')) {
    /**
     * @param bool $removeCachedValueIfExists
     *
     * @return User|null
     */
    function user(bool $removeCachedValueIfExists = false): ?User {
        return UserService::getUser($removeCachedValueIfExists);
    }
}
