<?php

namespace App\Http\Middleware;

use App\Models\Endpoint;
use App\Models\Scope;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app.layout';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        /* @var User $user */
        $user = $request->user();

        return array_merge(parent::share($request), [
            'user' => fn() => $user?->only('id', 'name', 'avatar', 'scopes'),
            'config' => fn() => [
                'app_name' => config('app.name', 'Open Twitch API Interface'),
                'app_name_short' => config('app.name_short', 'OTAI'),
                'theme' => config('theme'),
                'scopes' => [
                    'api' => $this->getScopes(),
                    'chat' => $this->getScopes(true),
                ],
            ],
        ]);
    }

    /**
     * @param bool $chatAndPupSub
     * @return array
     */
    protected function getScopes(bool $chatAndPupSub = false): array
    {
        return Scope::query()
            ->where('chat_and_pup_sub', $chatAndPupSub)
            ->orderBy('name')
            ->get()
            ->map(function (Scope $scope) {
                return [
                    'id' => $scope->name,
                    'description' => strip_tags(explode('<a', $scope->description)[0]),
                    'endpoints' => $this->getFormattedEndpoints($scope->endpoints()->orderBy('name')->get()),
                ];
            })->toArray();
    }

    /**
     * @param Collection|Endpoint $endpoints
     * @return array
     */
    protected function getFormattedEndpoints(Collection|Endpoint $endpoints): array
    {
        return $endpoints->map(function (Endpoint $endpoint) {
            return [
                'name' => $endpoint->name,
                'slug' => $endpoint->slug,
            ];
        })->toArray();
    }
}
