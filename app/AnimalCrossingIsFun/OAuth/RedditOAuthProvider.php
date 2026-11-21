<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use Rudolf\OAuth2\Client\Provider\Reddit;

/**
 * The upstream provider points at ssl.reddit.com, which now 301-redirects to
 * www.reddit.com. Guzzle drops the Authorization header on that cross-origin
 * redirect, so the token request reaches Reddit without credentials (401).
 */
class RedditOAuthProvider extends Reddit {

    #[\Override]
    public function getBaseAuthorizationUrl() {
        return 'https://www.reddit.com/api/v1/authorize';
    }

    #[\Override]
    public function getBaseAccessTokenUrl(array $params) {
        return 'https://www.reddit.com/api/v1/access_token';
    }

}
