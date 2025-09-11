<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // For each of the registered modules, include their routes and Views
        $modules = config("module.modules");

        foreach($modules as $module){
            // Load the routes for each of the modules
            if(file_exists(base_path().'/modules/'.$module.'/Routes/web.php')) {
                include base_path().'/modules/'.$module.'/Routes/web.php';
            }

            if(file_exists(base_path().'/modules/'.$module.'/Routes/api.php')) {
                include base_path().'/modules/'.$module.'/Routes/api.php';
            }

            // Load the views
            if(is_dir(base_path().'/modules/'.$module.'/Views')) {
                $this->loadViewsFrom(base_path().'/modules/'.$module.'/Views', $module);
            }
        }

    }
}
