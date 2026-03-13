<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use DateTime;

class PurgeOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge:old-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge old backup files older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $directoryPath = storage_path('app/backup');
        $backupFiles = File::glob("$directoryPath/*.gz");

        $arrBack = [];

        $currentDate = new DateTime();

        foreach ($backupFiles as $key => $backupFileName) 
        {
            $string = $backupFileName;
            $pattern = "/backup-(\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2})\.gz/";

            if (preg_match($pattern, $string, $matches)) {
                $datePart = $matches[1];
                $backupDate = DateTime::createFromFormat('Y-m-d-H-i-s', $datePart);

                // Calculate the difference in days between $backupDate and $currentDate
                $interval = $currentDate->diff($backupDate);
                $daysDifference = $interval->days;

                // Check if the backup file is less than 30 days old
                if ($daysDifference > 30) {
                    $command = 'sudo rm ' . $backupFileName;
                    exec($command);
                    $this->info("Deleted old backup file: $backupFileName");
                }
            } else {
                echo "Date not found in the string.";
            }
        }

        // dd($arrBack);
        $this->info('Old data purging completed.');
    }
}
