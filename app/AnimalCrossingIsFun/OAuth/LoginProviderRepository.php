<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

class LoginProviderRepository
{

    /**
     * @return LoginProvider[]
     */
    public function getAll(): array {
        return [
            $this->getRedditProvider(),
            $this->getGoogleProvider(),
        ];
    }

    public function getRedditProvider(): LoginProvider
    {
        return new RedditProvider();
    }

    public function getGoogleProvider(): LoginProvider
    {
        return new GoogleProvider();
    }
}