<?php


namespace Brianclogan\DuskRecordings;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'screenrecording');

        $this->publishes([
            __DIR__ . '../config/screenrecording.php' => config_path('screenrecording.php')
        ]);

        if(!$this->app->environment('production')) {
            Route::group([
                'prefix' => '_screenrecording',
                'domain' => null,
            ], function() {
                Route::get('/bootstrap', function () {
                    return View::make('screenrecording::bootstrap');
                });
            });
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\DuskCommand::class,
                Console\DuskFailsCommand::class,
                Console\MakeCommand::class,
                Console\PageCommand::class,
                Console\PurgeCommand::class,
                Console\ComponentCommand::class,
                Console\ChromeDriverCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/screenrecording.php', 'screenrecording'
        );
    }
}
