<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        $this->binds();
    }

    /**
     * @return void
     */
    protected function binds(): void
    {
        app()->bind(
            \Illuminate\Pagination\LengthAwarePaginator::class,
            \NormanHuth\HelpersLaravel\Support\LengthAwarePaginator::class
        );
    }
}
