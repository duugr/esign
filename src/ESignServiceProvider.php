<?php

namespace ESign;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ESignServiceProvider extends ServiceProvider
{
    /**
     * If is defer.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the service.
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__) . '/config/esign.php' => config_path('esign.php'),],
                'esign'
            );
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('esign');
        }
    }

    /**
     * Register the service.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/esign.php', 'esign');

        $this->app->singleton('esign', function () {
            return new ESign(config('esign.app_id'), config('esign.secret'), config('esign.env'));
        });
    }

    /**
     * Get services.
     *
     * @return array
     */
    public function provides()
    {
        return ['esign'];
    }
}
