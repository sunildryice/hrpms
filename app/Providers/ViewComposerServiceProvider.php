<?php

namespace App\Providers;

use App\View\Components\Breadcrumb;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        view()->composer(
            [
                'layouts.header'
            ],
            'App\Http\ViewComposers\HeaderComposer'
        );

        view()->composer(
            [
                'layouts.sidebar'
            ],
            'App\Http\ViewComposers\SideBarComposer'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('breadcrumb', function ($items = []) {
            return Blade::renderComponent(new Breadcrumb($items));
        });

    }
}
