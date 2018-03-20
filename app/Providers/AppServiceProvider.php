<?php

namespace App\Providers;

use App\Models\Job;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\ServiceProvider;
use Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Carbon\Carbon::setLocale('zh');

        Queue::after(function (JobProcessed $event) {
            $data = unserialize($event->job->payload()['data']['command']);
            $id = $data->_job->id;
            $job = Job::find($id);
            $job->status = config('enums.job_status.success');
            $job->save();
        });

        Queue::failing(function (JobFailed $event) {
            $data = unserialize($event->job->payload()['data']['command']);
            $id = $data->_job->id;
            $job = Job::find($id);
            $job->status = config('enums.job_status.fail');
            $job->desc = $event->exception->getMessage();
            $job->save();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
        //
    }
}
