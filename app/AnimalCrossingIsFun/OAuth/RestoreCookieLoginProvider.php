<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class RestoreCookieLoginProvider extends LoginProvider {

    public function getProviderName(): string {
        return 'Should Not Implement This';
    }

    public function getRouteName(): string {
        return 'should-not-implement-this';
    }

    public function parseResourceOwnerResponse(array|ResourceOwnerInterface $resourceOwner): array {
        return [];
    }
}
