<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Tests\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Mave\AnimalCrossingIsFun\OAuth\LoginFailedException;
use Mave\AnimalCrossingIsFun\OAuth\LoginProvider;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginProviderTest extends TestCase {

    private AbstractProvider&MockObject $oauthProvider;

    private LoginProvider $loginProvider;

    protected function setUp(): void {
        $_SESSION = [];

        $this->oauthProvider = $this->createMock(AbstractProvider::class);
        $this->oauthProvider->method('getAuthorizationUrl')->willReturn('https://provider.test/authorize');
        $this->oauthProvider->method('getState')->willReturn('expected-state');

        $this->loginProvider = new TestLoginProvider($this->oauthProvider);
    }

    protected function tearDown(): void {
        $_SESSION = [];
    }

    public function testLoginSucceedsWithMatchingState(): void {
        $this->loginProvider->getAuthorizationUrl();
        $this->expectTokenExchange();

        $this->loginProvider->loginUser($this->callbackRequest(['code' => 'abc', 'state' => 'expected-state']));

        self::assertSame(['id' => 'user-1', 'name' => 'Isabelle'], $_SESSION['user']);
    }

    public function testLoginFailsWithMismatchingState(): void {
        $this->loginProvider->getAuthorizationUrl();
        $this->oauthProvider->expects(self::never())->method('getAccessToken');

        $this->expectException(LoginFailedException::class);
        $this->loginProvider->loginUser($this->callbackRequest(['code' => 'abc', 'state' => 'forged-state']));
    }

    public function testLoginFailsWithoutStateInCallback(): void {
        $this->loginProvider->getAuthorizationUrl();
        $this->oauthProvider->expects(self::never())->method('getAccessToken');

        $this->expectException(LoginFailedException::class);
        $this->loginProvider->loginUser($this->callbackRequest(['code' => 'abc']));
    }

    public function testLoginFailsWhenLoginWasNotStartedInThisSession(): void {
        $this->oauthProvider->expects(self::never())->method('getAccessToken');

        $this->expectException(LoginFailedException::class);
        $this->loginProvider->loginUser($this->callbackRequest(['code' => 'abc', 'state' => 'expected-state']));
    }

    public function testStateCanOnlyBeUsedOnce(): void {
        $this->loginProvider->getAuthorizationUrl();
        $this->expectTokenExchange();
        $this->loginProvider->loginUser($this->callbackRequest(['code' => 'abc', 'state' => 'expected-state']));

        $this->expectException(LoginFailedException::class);
        $this->loginProvider->loginUser($this->callbackRequest(['code' => 'abc', 'state' => 'expected-state']));
    }

    private function expectTokenExchange(): void {
        $resourceOwner = $this->createStub(ResourceOwnerInterface::class);
        $resourceOwner->method('toArray')->willReturn(['id' => 'user-1', 'name' => 'Isabelle']);

        $this->oauthProvider
            ->expects(self::once())
            ->method('getAccessToken')
            ->with(self::anything(), ['code' => 'abc'])
            ->willReturn(new AccessToken(['access_token' => 'token']));
        $this->oauthProvider->method('getResourceOwner')->willReturn($resourceOwner);
    }

    private function callbackRequest(array $queryParams): ServerRequest {
        return new ServerRequest('GET', '/auth/test/callback')->withQueryParams($queryParams);
    }

}

class TestLoginProvider extends LoginProvider {

    public function __construct(AbstractProvider $oauthProvider) {
        parent::__construct();

        $this->oauthProvider = $oauthProvider;
    }

    public function getProviderName(): string {
        return 'Test';
    }

    public function getRouteName(): string {
        return 'test';
    }

    public function parseResourceOwnerResponse(array|ResourceOwnerInterface $resourceOwner): array {
        return $resourceOwner->toArray();
    }

    protected function setUserCookie(array $params): LoginProvider {
        // The real cookie needs Redis and headers; not relevant for these tests
        return $this;
    }

}
