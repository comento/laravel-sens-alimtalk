<?php


namespace Comento\SensAlimtalk;

use Illuminate\Support\ServiceProvider;

class SensAlimtalkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sens-alimtalk.php', 'sens-alimtalk');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/sens-alimtalk.php' => config_path('sens-alimtalk.php')], 'config');

        $this->app->when(SensAlimtalkChannel::class)
            ->needs(SensAlimtalk::class)
            ->give(function ($app) {
                return new SensAlimtalk(
                    config('sens-alimtalk.access_key'),
                    config('sens-alimtalk.secret_key'),
                    config('sens-alimtalk.service_id')
                );
            });
    }
}
