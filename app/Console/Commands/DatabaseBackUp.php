<?php
  
namespace App\Console\Commands;
  
use Illuminate\Console\Command;
  
class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:db';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';
  
    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        // Get the database connection details from environment variables

        $databaseName = 'cenwh';
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        // Specify the backup file name (you can customize this)
        $backupFileName = 'backup-' . date('Y-m-d-H-i-s');

        // Specify the path to store the backup file
        $backupFilePath = storage_path('app/backup/' . $backupFileName);

        // Create the backup using mysqldump and compress it with gzip
        $command = sprintf(
            'mysqldump --user=%s --password=%s %s | gzip > %s.gz',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($databaseName),
            escapeshellarg($backupFilePath)
        );

        // Execute the backup command
        exec($command);

        // Check if the backup was successful
        if (file_exists($backupFilePath . '.gz')) {
            $this->info('Database backup completed successfully.');
        } else {
            $this->error('Database backup failed.');
        }
    }
}