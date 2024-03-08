<?php

namespace App\Http\Clients;

use App\Models\Endpoint;
use App\Models\User;
use App\Models\Request;
use App\Exceptions\TwitchApiException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ExpectedValues;
use NormanHuth\Helpers\Str;

class TwitchHelix
{
    /**
     * The Twitch Helix API base url.
     *
     * @var string
     */
    protected string $baseURL = 'https://api.twitch.tv/helix';

    /**
     * The Endpoint instance.
     *
     * @var Endpoint
     */
    protected Endpoint $endpoint;

    /**
     * The User instance.
     *
     * @var User
     */
    protected User $user;

    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Writing Log Message.
     *
     * @param mixed  $message
     * @param string $level
     * @param array  $context
     *
     * @return void
     */
    protected function log(
        mixed $message,
        #[ExpectedValues(values: ['alert', 'critical', 'debug', 'emergency', 'error', 'info', 'notice', 'warning'])]
        string $level = 'error',
        array $context = []
    ): void {
        $message = formatLogMessage($message);

        Log::channel('twitch-api')->{$level}($message, $context);
    }

    /**
     * Make a "User access token" request to Twitch API.
     *
     * @param User         $user
     * @param array|null   $requestBody
     * @param array|string $requestQueryString
     *
     * @throws TwitchApiException
     * @return Request
     */
    public function userRequest(User $user, ?array $requestBody, array|string $requestQueryString = ''): Request
    {
        $this->user = $user;

        if (is_array($requestQueryString)) {
            $requestQueryString = http_build_query($requestQueryString);
        }

        if ($requestQueryString && !str_starts_with($requestQueryString, '?')) {
            $requestQueryString = '?' . $requestQueryString;
        }

        $endpointUrl = $this->baseURL.$this->endpoint->route.$requestQueryString;
        $method = Str::lower($this->endpoint->method);

        if (!empty($requestBody)) {
            foreach ($requestBody as $key => $value) {
                if ($requestQueryString) {
                    $requestQueryString.= '&';
                }
                if (is_bool($value)) {
                    $value = (int) $value;
                }
                $requestQueryString.= $key.'='.$value;
            }
        }

        if ($requestQueryString && !str_starts_with($requestQueryString, '?')) {
            $requestQueryString = '?' . $requestQueryString;
        }

        $url = $this->baseURL.$this->endpoint->route.$requestQueryString;

        $client = Http::withToken($user->token)
            ->withHeaders([
                'Client-Id' => config('services.twitch.client_id'),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);

        /* @var Response $response */
        $response = empty($requestBody) ? $client->{$method}($url) : $client->{$method}($url, $requestBody);

        if ($response->clientError() && $response->json('message') == 'Invalid OAuth token') {
            $this->refreshUserToken();

            return $this->userRequest($user, $requestBody, $requestQueryString);
        }

        return $this->user->requests()->create([
            'endpoint_id' => $this->endpoint->getKey(),
            'url' => $endpointUrl,
            'request_body' => $requestBody,
            'response_body' => (array) $response->json(),
            'response_code' => $response->status(),
        ]);
    }

    /**
     * Refresh "User access token".
     *
     * @throws TwitchApiException
     */
    public function refreshUserToken(): void
    {
        $queries = [
            'client_id' => config('services.twitch.client_id'),
            'client_secret' => config('services.twitch.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->user->refresh_token,
        ];

        $response = Http::withToken(config('services.twitch.client_secret'))
            ->withHeaders([
                'Client-Id' => config('services.twitch.client_id'),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->post('https://id.twitch.tv/oauth2/token?' . http_build_query($queries));

        if ($response->clientError()) {
            $this->user->update([
                'token' => null,
                'refresh_token' => null,
                'scopes' => null,
            ]);

            Auth::logout();

            if ($response->json('message') == 'Invalid refresh token') {
                throw new TwitchApiException('Could not refresh User access token. Please renew Your Authentication.');
            }

            $this->log([
                'user' => $this->user->getKey(),
                'endpoint' => $this->endpoint->getKey(),
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if (is_string($response->json('message'))) {
                throw new TwitchApiException('Twitch API Error: ' . $response->json('message'));
            }

            throw new TwitchApiException('Unknown Twitch API Error.');
        }

        $this->user->update([
            'token' => $response->json('access_token'),
            'refresh_token' => $response->json('refresh_token'),
            'scopes' => $response->json('scope'),
            'expired_at' => now()->addSeconds($response->json('expires_in')),
        ]);
    }
}
