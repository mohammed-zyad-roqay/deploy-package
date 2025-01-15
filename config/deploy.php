<?php 


return [
    'config_path' => env('DEPLOY_CONFIG_PATH', base_path('env_deploy_commands')),
    'environments' => ['test', 'production', 'dev'],
    'log_path' => env('DEPLOY_LOG_PATH', 'logs'),
];