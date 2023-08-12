<?php

namespace kmacute\GqlHelper;

use Illuminate\Support\ServiceProvider;
use kmacute\GqlHelper\Commands\MakeGql;
use kmacute\GqlHelper\Commands\MakeGqlAuth;

class GqlHelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerCommands();
    }

    public function register()
    {
    }

    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeGql::class,
                MakeGqlAuth::class,
            ]);
        }
    }
}
