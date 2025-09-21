<?php

namespace App\Jobs;

use BaseApi\Queue\Job;
use BaseApi\App;

class BackupDatabaseJob extends Job
{
    protected int $maxRetries = 2;
    protected int $retryDelay = 300; // 5 minutes
    
    public function __construct(
        private ?string $backupPath = null,
        private bool $compress = true
    ) {
        // Set default backup path if none provided
        $this->backupPath = $backupPath ?? App::storagePath('backups');
    }
    
    public function handle(): void
    {
        $config = App::config('database');
        
        // Create backup directory if it doesn't exist
        if (!is_dir($this->backupPath)) {
            if (!mkdir($this->backupPath, 0755, true)) {
                throw new \Exception("Could not create backup directory: {$this->backupPath}");
            }
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        if ($this->compress) {
            $filename .= '.gz';
        }
        
        $fullPath = $this->backupPath . '/' . $filename;
        
        // Perform backup based on database type
        $driver = $config['driver'] ?? 'sqlite';
        
        switch ($driver) {
            case 'sqlite':
                $this->backupSqlite($config, $fullPath);
                break;
            case 'mysql':
                $this->backupMysql($config, $fullPath);
                break;
            case 'pgsql':
                $this->backupPostgres($config, $fullPath);
                break;
            default:
                throw new \Exception("Backup not supported for database driver: {$driver}");
        }
        
        error_log("Database backup completed: {$fullPath}");
        
        // Clean up old backups (keep last 10)
        $this->cleanupOldBackups();
    }
    
    private function backupSqlite(array $config, string $fullPath): void
    {
        $dbPath = $config['name'] ?? App::storagePath('database.sqlite');
        
        if (!file_exists($dbPath)) {
            throw new \Exception("SQLite database file not found: {$dbPath}");
        }
        
        if ($this->compress) {
            // Copy and compress
            $sql = file_get_contents($dbPath);
            $compressed = gzencode($sql);
            if (file_put_contents($fullPath, $compressed) === false) {
                throw new \Exception("Failed to create compressed backup");
            }
        } else {
            // Simple copy
            if (!copy($dbPath, $fullPath)) {
                throw new \Exception("Failed to copy SQLite database");
            }
        }
    }
    
    private function backupMysql(array $config, string $fullPath): void
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['name'] ?? 'baseapi';
        $username = $config['user'] ?? 'root';
        $password = $config['password'] ?? '';
        
        $command = sprintf(
            'mysqldump -h%s -P%d -u%s %s %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            !empty($password) ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($database)
        );
        
        if ($this->compress) {
            $command .= ' | gzip > ' . escapeshellarg($fullPath);
        } else {
            $command .= ' > ' . escapeshellarg($fullPath);
        }
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("MySQL backup failed with return code: {$returnCode}");
        }
    }
    
    private function backupPostgres(array $config, string $fullPath): void
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 5432;
        $database = $config['name'] ?? 'baseapi';
        $username = $config['user'] ?? 'postgres';
        
        // Set environment variables for pg_dump
        $env = [
            'PGPASSWORD' => $config['password'] ?? '',
        ];
        
        $command = sprintf(
            'pg_dump -h %s -p %d -U %s %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($database)
        );
        
        if ($this->compress) {
            $command .= ' | gzip > ' . escapeshellarg($fullPath);
        } else {
            $command .= ' > ' . escapeshellarg($fullPath);
        }
        
        // Execute with environment variables
        $fullCommand = '';
        foreach ($env as $key => $value) {
            $fullCommand .= $key . '=' . escapeshellarg($value) . ' ';
        }
        $fullCommand .= $command;
        
        exec($fullCommand, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("PostgreSQL backup failed with return code: {$returnCode}");
        }
    }
    
    private function cleanupOldBackups(): void
    {
        $files = glob($this->backupPath . '/backup_*.sql*');
        
        if (count($files) <= 10) {
            return; // Keep all if 10 or fewer
        }
        
        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Remove files beyond the 10 most recent
        $filesToDelete = array_slice($files, 10);
        
        foreach ($filesToDelete as $file) {
            if (unlink($file)) {
                error_log("Cleaned up old backup: {$file}");
            }
        }
    }
    
    public function failed(\Throwable $exception): void
    {
        error_log("Database backup failed: " . $exception->getMessage());
        parent::failed($exception);
        
        // Notify administrators about backup failure
        // dispatch(new NotifyAdminsJob(
        //     "Database Backup Failed",
        //     "Automatic database backup failed: {$exception->getMessage()}"
        // ));
    }
}
