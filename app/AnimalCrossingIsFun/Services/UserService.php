<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Mave\AnimalCrossingIsFun\Dto\User;

class UserService {

    /**
     * @var null|User
     */
    private static $user = false;

    public static function getUser(): ?User {
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
