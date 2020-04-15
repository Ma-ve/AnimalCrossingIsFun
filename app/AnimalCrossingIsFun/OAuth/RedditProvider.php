<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use Nyholm\Psr7\ServerRequest as Request;

class RedditProvider {

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $redirectUrl;

    public function __construct() {
        $this->clientId = env('REDDIT_CLIENT_ID');
        $this->clientSecret = env('REDDIT_CLIENT_SECRET');
        $this->redirectUrl = env('REDDIT_CALLBACK_URL');
    }

    public function start() {
        $url = "https://www.reddit.com/api/v1/authorize?";
        $queryParams = http_build_query([
            'client_id'     => $this->clientId,
            'response_type' => 'code',
            'state'         => $this->setState(),
            'redirect_uri'  => $this->redirectUrl,
            'duration'      => 'temporary',
            'scope'         => 'identity',
        ]);

        header("Location: {$url}{$queryParams}");
        exit;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function handleCallback(Request $request) {
        $queryParams = $request->getQueryParams();
        if(isset($queryParams['error'])) {
            return $queryParams;
        }

        if(
            !$queryParams ||
            !isset($queryParams['code']) ||
            !isset($queryParams['state']) ||
            $queryParams['state'] !== $this->getState()
        ) {
            throw new \Exception('Unexpected data in request');
        }

        $ch = curl_init('https://www.reddit.com/api/v1/access_token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type'   => 'authorization_code',
                'code'         => $queryParams['code'],
                'redirect_uri' => $this->redirectUrl,
            ]),
            CURLOPT_TIMEOUT        => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if($info['http_code'] !== 200 || !$result) {
            throw new \Exception('Unexpected responsef rom reddit');
        }
//        '{"access_token": "6953484-g-PEykYxz_H_zlgvVTb0XDQCEa0", "token_type": "bearer", "expires_in": 3600, "scope": "identity"}' (length=120)
    }

    /**
     * @return bool|mixed
     */
    private function getState() {
        return $_SESSION['state'] ?? false;
    }

    /**
     * @return string
     */
    private function setState() {
        return $_SESSION['state'] = uniqid();
    }

}
