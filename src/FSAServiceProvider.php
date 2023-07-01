<?php

namespace Tarek\Fsa;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class FSAServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        /*$this->mergeConfigFrom(__DIR__ . 'config/fsa.php',"auth");*/
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
        Config::set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'admins'
        ]);
        Config::set('auth.providers.admins', [
            'driver' => 'eloquent',
            'model' => \App\Models\FsaAdmin::class
        ]);
        Config::set('auth.passwords.admins', [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ]);
        $this->addPublishGroup('fsa',[
            __DIR__ . '/Http/'                      => app_path('/Http/'),
            __DIR__ . '/Models/FsaAdmin.php'        => app_path('/Models/FsaAdmin.php'),
            __DIR__ . '/routes/'                    => base_path('/routes/')
        ]);
    }
}
