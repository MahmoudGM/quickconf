<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use View;
use Illuminate\Http\Request; 
use Config;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request )
    {
        
        Schema::defaultStringLength(191);

        $lang = Config::get('lang') ;
        if ($lang == NULL){
            $lang = 'en';
        }
        
        View::composer('*', function($view){
            View::share('view_name', $view->getName());
        });
        View::share('lang', $lang);
        



    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
