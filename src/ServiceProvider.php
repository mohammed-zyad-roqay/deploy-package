<?php 

namespace Deploy\DeployPackage;

use Deploy\DeployPackage\Commands\DeployCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                DeployCommand::class,
            ]);
        }
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/deploy.php' => config_path('deploy.php'),
        ], 'deploy-config');
    }
}