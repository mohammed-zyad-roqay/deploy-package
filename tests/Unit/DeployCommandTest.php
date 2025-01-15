<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Deploy\DeployPackage\Commands\DeployCommand;
use Deploy\DeployPackage\ServiceProvider;

class DeployCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('deploy.config_path', __DIR__ . '/../../env_deploy_commands');
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Register command in container
        $this->app->singleton(DeployCommand::class);
        
        // Create test config directory
        if (!is_dir(__DIR__ . '/../../env_deploy_commands')) {
            mkdir(__DIR__ . '/../../env_deploy_commands', 0755, true);
        }
        
        // Create test config file
        file_put_contents(
            __DIR__ . '/../../env_deploy_commands/test.json',
            json_encode([
                'servers' => [['ip' => '127.0.0.1', 'user' => 'test']],
                'commands' => ['echo "test"']
            ])
        );
    }

    public function testDeployCommandExecutesSuccessfully()
    {
        $this->artisan('deploy test')
            ->assertExitCode(0);
    }
}