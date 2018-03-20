<?php

namespace App\Jobs;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Handlers\ExcelImportHandler;

class ImportExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $path, $_job;
    public $timeout = 600;

    /**
     * Create a new job instance.
     *
     * @param $path
     * @param Job $job
     */
    public function __construct($path, Job $job)
    {
        $this->path = $path;
        $this->_job = $job;
    }

    /**
     * Execute the job.
     *
     * @param ExcelImportHandler $importHandler
     * @return void
     */
    public function handle(ExcelImportHandler $importHandler)
    {
        $importHandler->import($this->path, $this->_job->type);
    }

}
