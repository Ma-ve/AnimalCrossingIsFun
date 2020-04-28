<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use Nyholm\Psr7\ServerRequest as Request;

class RedditProvider extends LoginProvider {

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
        parent::__construct();

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
     * @return void
     * @throws \Exception
     */
    public function handleCallback(Request $request) {
        $user = $this->makeUserRequest(
            $this->getAccessToken(
                $this->getCodeFromRequest($request)
            )
        );

        $params = [
            'id'       => $user['id'],
            'username' => $user['name'],
        ];
        parent
            ::setUserData($params)
            ->setUserCookie($params);
    }

    /**
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */
    private function getCodeFromRequest(Request $request): string {
        $queryParams = $request->getQueryParams();
        if(isset($queryParams['error'])) {
            throw new \Exception('Invalid response: ' . print_r($queryParams));
//            throw (new HandleCallbackException('Invalid response'))
//                ->setQueryParams($queryParams);
        }

        if(
            !$queryParams ||
            !isset($queryParams['code']) ||
            !isset($queryParams['state']) ||
            $queryParams['state'] !== $this->getState()
        ) {
            throw new \Exception('Unexpected data in request');
        }

        return $queryParams['code'];
    }

    private function getAccessToken(string $code): string {
        $ch = curl_init('https://www.reddit.com/api/v1/access_token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type'   => 'authorization_code',
                'code'         => $code,
                'redirect_uri' => $this->redirectUrl,
            ]),
            CURLOPT_TIMEOUT        => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if($info['http_code'] !== 200 || !$result) {
            throw new \Exception('Unexpected response from reddit [1]');
        }

        $json = json_decode($result, true);
        if(!$json || json_last_error() !== JSON_ERROR_NONE || !isset($json['access_token'])) {
            throw new \Exception('Unexpected response from reddit [2]');
        }

        return $json['access_token'];
    }

    /**
     * @param string $accessToken
     *
     * @return array
     * @throws \Exception
     */
    private function makeUserRequest(string $accessToken): array {
        $ch = curl_init('https://oauth.reddit.com/api/v1/me');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Accept: application/json',
            ],
            CURLOPT_USERAGENT      => 'animalcrossing is fun (by /u/Mavee)',
            CURLOPT_TIMEOUT        => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        if(!$result) {
            throw new \Exception('Could not decode reddit response for v1/me');
        }

        $json = json_decode($result, true);
        if(!$json || json_last_error() !== JSON_ERROR_NONE || !isset($json['is_employee'])) {
            throw new \Exception('Unexpected reddit response for v1/me');
        }

        if(!isset($json['id']) || !isset($json['name'])) {
            throw new \Exception('Expected id and name in reddit response');
        }

        return $json;
    }

    /**
     * @return bool|mixed
     */
    private function getState() {
        $state = $_SESSION['state'] ?? false;
        unset($_SESSION['state']);

        return $state;
    }

    /**
     * @return string
     */
    private function setState() {
        return $_SESSION['state'] = uniqid();
    }

}
