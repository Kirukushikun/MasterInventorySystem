<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:db {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the database from a backup file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the backup file name from the command argument
        $backupFile = 'storage/app/backup/' . $this->argument('file');

        // Get the database connection details from environment variables
        $databaseName = 'cenwh';
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        // Check if the backup file exists
        if (!file_exists($backupFile)) {
            $this->error('Backup file does not exist.');
            return;
        }

        // Restore the database using the gzip-compressed backup file
        $command = sprintf(
            'sudo zcat %s | mysql --user=%s --password=%s %s',
            escapeshellarg($backupFile),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($databaseName)
        );

        // Execute the restore command
        exec($command);

        // Check if the restore was successful
        $this->info('Database restore completed successfully.');
    }
}
