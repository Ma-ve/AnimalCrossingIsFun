<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GoogleProvider extends LoginProvider {

    public function __construct() {
        parent::__construct();

        $this->oauthProvider = new Google([
            'clientId' => env('GOOGLE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_CLIENT_SECRET'),
            'redirectUri' => env('GOOGLE_CALLBACK_URL'),
        ]);
    }

    public function getProviderName(): string {
        return 'Google';
    }

    public function getRouteName(): string {
        return 'google';
    }

    public function parseResourceOwnerResponse(array|ResourceOwnerInterface $resourceOwner): array {
        $array = $resourceOwner->toArray();

        return [
            'id' => $array['sub'],
            'name' => $array['given_name'] ?? $array['email'],
        ];
    }

}
