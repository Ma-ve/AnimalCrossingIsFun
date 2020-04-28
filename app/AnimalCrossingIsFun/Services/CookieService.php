<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

class CookieService {

    const COOKIE_REMEMBER_ME = 'remember-me';

    /**
     * @var CacheService
     */
    private $cacheService;

    public function __construct() {
        $this->cacheService = new CacheService();
    }

    /**
     * @param null|array $data
     *
     * @return null
     */
    public function setLoginCookie(?array $data) {
        if(null === $data) {
            $this->returnUnsetCookie();

            return;
        }

        $key = md5(uniqid());
        $this->cacheService->set($this->getLoginCookieValue($key), json_encode($data), 60 * 60 * 24 * 30);
        $this->setCookie($key, 60 * 60 * 24 * 30);
    }

    /**
     * @return array|null
     */
    public function getDataFromLoginCookie(): ?array {
        $cookieValue = $_COOKIE[self::COOKIE_REMEMBER_ME] ?? false;
        if(!$cookieValue) {
            return null;
        }

        $loginCookieValue = $this->getLoginCookieValue($cookieValue);

        $dataString = $this->cacheService->get($loginCookieValue);
        if(null === $dataString) {
            return $this->returnUnsetCookie();
        }

        $this->cacheService->delete($loginCookieValue);

        $json = json_decode($dataString, true);
        if(!$json) {
            return $this->returnUnsetCookie();
        }

        // Cookie has been used once, so let's set a new one
        $this->setLoginCookie($json);

        return $json;
    }

    private function getLoginCookieValue(string $key): string {
        return self::COOKIE_REMEMBER_ME . '.' . $key;
    }

    /**
     * @return null
     */
    private function returnUnsetCookie() {
        $this->setCookie('', -999);

        return null;
    }

    /**
     * @param string $value
     * @param int    $expiryInSeconds
     */
    private function setCookie(string $value, int $expiryInSeconds) {
        setcookie(self::COOKIE_REMEMBER_ME, $value, time() + $expiryInSeconds, '/', '', true, true);
    }

}
