<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use Mave\AnimalCrossingIsFun\Services\CookieService;

class LoginProvider {

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct() {
        $this->cookieService = new CookieService();
    }

    /**
     * @param array $params
     *
     * @return LoginProvider
     */
    protected function setUserData(array $params) {
        $_SESSION['user'] = $params;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    protected function setUserCookie(array $params) {
        $this->cookieService->setLoginCookie($params);

        return $this;
    }

    /**
     * @return $this
     */
    public function restoreUserFromLoginCookie() {
        return $this->setUserData(
            $this->cookieService->getDataFromLoginCookie() ?? []
        );
    }

}
