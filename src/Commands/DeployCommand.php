<?php


namespace Deploy\DeployPackage\Commands;


use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Deploy\DeployPackage\DeploymentLogWriter;


class DeployCommand extends Command
{
    protected $signature = 'deploy {environment} {--ssh}';
    protected $description = 'Deploy application to the specified environment using SSH commands';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $environment = $this->argument('environment');
        $useSsh = $this->option('ssh');

        $config = $this->getEnvironmentCommands($environment);

        if (empty($config)) {
            $this->error("No configuration found for '$environment'.");
            return 1;
        }

        $commands = $config['commands'] ?? [];
        $servers = $config['servers'] ?? [];

        if (empty($servers)) {
            $this->error("No servers defined for '$environment'.");
            return 1;
        }

        $sshKeyPath = null;
        if ($useSsh) {
            while (true) {
                $sshKeyPath = $this->ask('Enter the path to your SSH private key');
                if (file_exists($sshKeyPath)) {
                    $this->info("SSH key validated: $sshKeyPath");
                    break;
                } else {
                    $this->error("Invalid SSH key path. Please try again.");
                }
            }
        }

        $logOutput = [];

        // Loop through each server and execute the commands
        foreach ($servers as $server) {
            $ip = $server['ip'];
            $user = $server['user'];

            $this->info("Starting deployment to server: $user@$ip");
            $logOutput[] = "### Server: $user@$ip ###";

            // Execute each command
            foreach ($commands as $command) {
                $sshCommand = $useSsh
                    ? "ssh -i $sshKeyPath $user@$ip '$command'"
                    : "ssh $user@$ip '$command'";

                $this->info("Running command: $command");
                $logOutput[] = "Running command: $command";

                $process = Process::fromShellCommandline($sshCommand);
                $process->run();

                if ($process->isSuccessful()) {
                    $output = $process->getOutput();
                    $this->info("Command output: $output");
                    $logOutput[] = "Command output: $output";
                } else {
                    $errorOutput = $process->getErrorOutput();
                    $this->error("Error executing command on $ip: $errorOutput");
                    $logOutput[] = "Error executing command: $errorOutput";
                    break;
                }
            }

            $this->info("Completed deployment to server: $user@$ip");
            $logOutput[] = "Completed deployment to server: $user@$ip";
        }

        // Write log to file
        $logFile = DeploymentLogWriter::writeLog($logOutput, $environment);

        $this->info("Deployment log saved to: $logFile");

        $this->info("Deployment to all servers for '$environment' completed.");

        return 0;
    }

    protected function getEnvironmentCommands(string $environment): array
    {
        // check if environment is defined in the configuration
        if(!in_array($environment, config('deploy.environments', []))) {
            $this->error("Environment '$environment' is not defined in the configuration.");
            return [];
        }

        // check if the file exists
        $basePath = config('deploy.config_path', base_path('env_deploy_commands'));
        $path = $basePath . "/{$environment}.json";
        if (!file_exists($path)) {
            $this->error("Command file for environment '$environment' does not exist: $path");
            return [];
        }

        $data = json_decode(file_get_contents($path), true);

        // check if the file is valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Error parsing JSON file: " . json_last_error_msg());
            return [];
        }

        return $data;
    }
}
