<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateJobNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-job-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reformat all existing job numbers to the sequential JOB-YYYY-XXX format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jobs = \App\Models\ServiceJob::orderBy('created_at', 'asc')->get();
        $counters = [];

        $this->info('Starting job number reformatting...');

        foreach ($jobs as $job) {
            $year = $job->created_at->format('Y');
            if (!isset($counters[$year])) {
                $counters[$year] = 1;
            }

            $currentNumber = str_pad($counters[$year], 3, '0', STR_PAD_LEFT);
            $newJobNumber = "JOB-{$year}-{$currentNumber}";
            
            $job->job_number = $newJobNumber;
            $job->save();

            $this->line("Updated Job ID {$job->id} to {$newJobNumber}");
            $counters[$year]++;
        }

        $this->info('Reformatting complete!');
    }
}
