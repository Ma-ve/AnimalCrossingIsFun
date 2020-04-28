<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Mave\AnimalCrossingIsFun\Dto\User;

class UserService {

    /**
     * @var null|User
     */
    private static $user = false;

    /**
     * @param bool $removeCachedValueIfExists
     *
     * @return User|null
     */
    public static function getUser(bool $removeCachedValueIfExists): ?User {
        if($removeCachedValueIfExists) {
            self::$user = false;
        }

        if(false !== self::$user) {
            return self::$user;
        }

        if(empty($_SESSION) || !isset($_SESSION['user'])) {
            self::$user = null;

            return self::$user;
        }

        self::$user = new User($_SESSION['user']);

        return self::$user;
    }

}
