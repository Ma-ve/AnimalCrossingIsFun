<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Mave\AnimalCrossingIsFun\Services\CookieService;
use Nyholm;

abstract class LoginProvider {

    protected CookieService $cookieService;

    protected AbstractProvider $oauthProvider;

    public function __construct() {
        $this->cookieService = new CookieService();
    }

    abstract public function getProviderName(): string;

    abstract public function getRouteName(): string;

    abstract public function parseResourceOwnerResponse(array|ResourceOwnerInterface $resourceOwner): array;

    /**
     * @param array $params
     *
     * @return LoginProvider
     */
    protected function setUserData(array $params): self {
        $_SESSION['user'] = $params;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    protected function setUserCookie(array $params): self {
        $this->cookieService->setLoginCookie($params);

        return $this;
    }

    /**
     * @return $this
     */
    public function restoreUserFromLoginCookie(): self {
        return $this->setUserData(
            $this->cookieService->getDataFromLoginCookie() ?? []
        );
    }

    public function getOAuthProvider(): AbstractProvider
    {
        return $this->oauthProvider;
    }

    public function loginUser(Nyholm\Psr7\ServerRequest $request): void {
        $queryParams = $request->getQueryParams();
        $resourceOwner = $this->oauthProvider->getResourceOwner(
            $this->oauthProvider->getAccessToken(new AuthorizationCode(), [
                'code' => $queryParams['code'] ?? null,
                'state' => $queryParams['code'] ?? null,
            ])
        );

        $params = $this->parseResourceOwnerResponse($resourceOwner);

        $this
            ->setUserData($params)
            ->setUserCookie($params);
    }

}
