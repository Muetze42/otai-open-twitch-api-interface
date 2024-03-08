<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use App\Models\Resource;
use App\Support\TwitchEndpoint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EndpointController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        inertiaFullShare('title', config('app.name', 'Open Twitch API Interface'));
        inertiaFullShare('description', 'Test the Twitch API directly in the browser. Just authenticate, choose your endpoint and go. All without programming anything.');

        return Inertia::render('Requests/Index', [
            'resources' => [
                'active' => $this->getResources(),
                'inactive' => $this->getResources(false),
            ],
        ]);
    }

    /**
     * @param bool $active
     * @return mixed
     */
    protected function getResources(bool $active = true)
    {
        return Resource::orderBy('name')
            ->get()
            ->map(function (Resource $resource) use ($active) {
                return [
                    'name' => $resource->name,
                    'items' => $this->getEndpoints($resource, $active),
                ];
            })->toArray();
    }

    /**
     * @param Resource $resource
     * @param bool     $active
     * @return array
     */
    protected function getEndpoints(Resource $resource, bool $active): array
    {
        /* @var Endpoint $endpoints */
        $endpoints = $resource->endpoints();

        return $endpoints
            ->where('active', $active)
            ->orderBy('name')
            ->get()
            ->map(function (Endpoint $endpoint) {
                return [
                    'name' => $endpoint->name,
                    'slug' => $endpoint->slug,
                    'description' => $endpoint->description,
                    'active' => $endpoint->active,
                    'scopes' => $endpoint->scopes->pluck('name')->toArray(),
                ];
            })->toArray();
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @return Response
     */
    public function show(Request $request, string $slug)
    {
        $endpoint = Endpoint::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        inertiaFullShare('title', $endpoint->name . ' - ' . config('app.name_short', 'Open Twitch API Interface'));
        inertiaFullShare('description', sentenceNotEnded(strip_tags($endpoint->description)) . ' by using the official Twitch API.');

        $user = $request->user();
        $scopes = $endpoint->scopes->pluck('name')->toArray();

        return Inertia::render('Endpoints/Show', [
            'endpoint' => $endpoint->getKey(),
            'form_data' => (new TwitchEndpoint($endpoint))->getOutput(),
            'scopes' => $scopes,
            'response_codes' => $endpoint->response_codes,
            'can_execute' => $user && (empty($scopes) || array_intersect($user->scopes, $scopes)),
        ]);
    }
}
