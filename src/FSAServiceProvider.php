<?php

namespace Tarek\Fsa;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\ServiceProvider;

class FSAServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/auth.php','auth');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
        $this->addPublishGroup('fsa',[
            __DIR__ . '/Http/' => app_path('/Http/'),
            __DIR__ . '/Models/FsaAdmin.php' => app_path('/Models/FsaAdmin.php'),
            __DIR__ . '/routes/' => base_path('/routes/')
        ]);
    }
}
