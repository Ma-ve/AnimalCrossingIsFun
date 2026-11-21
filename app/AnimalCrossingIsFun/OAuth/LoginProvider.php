<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Mave\AnimalCrossingIsFun\Services\CookieService;
use Nyholm;

abstract class LoginProvider {

    private const string SESSION_STATE_KEY = 'oauth2state';

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

    public function getAuthorizationUrl(): string {
        $url = $this->oauthProvider->getAuthorizationUrl();

        // Remember the random state, so the callback can prove it belongs to this login attempt
        $_SESSION[self::SESSION_STATE_KEY][$this->getRouteName()] = $this->oauthProvider->getState();

        return $url;
    }

    /**
     * @throws LoginFailedException
     */
    private function assertValidState(array $queryParams): void {
        $expectedState = $_SESSION[self::SESSION_STATE_KEY][$this->getRouteName()] ?? null;
        unset($_SESSION[self::SESSION_STATE_KEY][$this->getRouteName()]);

        $state = $queryParams['state'] ?? null;
        if(!is_string($expectedState) || !is_string($state) || !hash_equals($expectedState, $state)) {
            throw new LoginFailedException('Your login attempt expired, please try again.');
        }
    }

    public function loginUser(Nyholm\Psr7\ServerRequest $request): void {
        $queryParams = $request->getQueryParams();
        $this->assertValidState($queryParams);

        $resourceOwner = $this->oauthProvider->getResourceOwner(
            $this->oauthProvider->getAccessToken(new AuthorizationCode(), [
                'code' => $queryParams['code'] ?? null,
            ])
        );

        $params = $this->parseResourceOwnerResponse($resourceOwner);

        $this
            ->setUserData($params)
            ->setUserCookie($params);
    }

}
