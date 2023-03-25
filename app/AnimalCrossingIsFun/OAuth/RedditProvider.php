<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Nyholm\Psr7\ServerRequest as Request;
use Rudolf\OAuth2\Client\Provider\Reddit;

class RedditProvider extends LoginProvider {

    public function __construct() {
        parent::__construct();

        $this->oauthProvider = new Reddit([
            'clientId' => env('REDDIT_CLIENT_ID'),
            'clientSecret' => env('REDDIT_CLIENT_SECRET'),
            'redirectUri' => env('REDDIT_CALLBACK_URL'),
            'userAgent' => sprintf('web:%s:animalcrossing is fun (by /u/Mavee)', date('Y-m-d')),
        ]);
    }

    public function getProviderName(): string {
        return 'Reddit';
    }

    public function getRouteName(): string {
        return 'reddit';
    }

    public function parseResourceOwnerResponse(array|ResourceOwnerInterface $resourceOwner): array {
        return [
            'id' => $resourceOwner['id'],
            'name' => $resourceOwner['name'],
        ];
    }
}
