<?php 

namespace Deploy\DeployPackage;


class DeploymentLogWriter
{
    public static function writeLog($logData, $environment)
    {
        $timestamp = date('Y_m_d_H_i_s');
        $logPath = storage_path(config('deploy.log_path'));
        
        // Create directory if it doesn't exist
        if (!file_exists($logPath)) {
            mkdir($logPath, 0755, true);
        }

        $logFile = $logPath . "/deployment_{$environment}_{$timestamp}.log";
        
        if (file_put_contents($logFile, implode("\n", $logData)) === false) {
            throw new \RuntimeException("Failed to write to log file: $logFile");
        }
        
        return $logFile;
    }
}