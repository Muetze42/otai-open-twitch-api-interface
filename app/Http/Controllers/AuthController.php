<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Twitch\Provider as TwitchSocialiteProviders;
use SocialiteProviders\Manager\OAuth2\User as SocialiteUser;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirect(Request $request)
    {
        $scopes = array_merge(
            (array) $request->input('apiScopes', []),
            (array) $request->input('chatScopes', []),
        );

        /* @var TwitchSocialiteProviders $socialite */
        $socialite = Socialite::driver('twitch')->stateless();
        $socialite->setScopes($scopes);

        $request->session()->put('AuthCurrent', $request->input('current'));
        $request->session()->put('AuthRemember', $request->input('remember'));

        $url = $socialite->redirect()->getTargetUrl();

        return Inertia::location($url);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(Request $request)
    {
        /* @var SocialiteUser|TwitchSocialiteProviders $socialiteUser */
        $socialiteUser = Socialite::driver('twitch')->stateless()->user();

        $user = User::updateOrCreate(
            ['id' => $socialiteUser->getId()],
            [
                'name' => $socialiteUser->getName(),
                'login' => data_get($socialiteUser->user, 'login'),
                'avatar' => $socialiteUser->getAvatar(),
                'broadcaster_type' => data_get($socialiteUser->user, 'broadcaster_type'),
                'token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'scopes' => data_get($socialiteUser->accessTokenResponseBody, 'scope', []),
                'twitch_created_at' => $socialiteUser->user['created_at'],
            ]
        );

        $url = $request->session()->get('AuthCurrent');

        Auth::login($user, (bool) $request->session()->get('AuthRemember', false));

        $request->session()->forget([
            'AuthCurrent',
            'AuthRemember',
        ]);

        return $url ? redirect($url) : redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }
}
