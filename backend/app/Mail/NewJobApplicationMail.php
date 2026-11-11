<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewJobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('طلب توظيف جديد: ' . $this->application->job->title . ' - ' . $this->application->full_name)
                    ->view('emails.new-job-application');
    }
}
