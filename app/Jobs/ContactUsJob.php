<?php

namespace App\Jobs;

use App\Mail\ContactUsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ContactUsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $contactUsData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contactUsData = null)
    {
        $this->contactUsData = $contactUsData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new ContactUsMail($this->contactUsData);
        //        dd($this->contactUsData, env('CONTACT_US_RECEIVING_EMAIL'), env('MAIL_USERNAME'), env('MAIL_PASSWORD'));
        Mail::to(env('CONTACT_US_RECEIVING_EMAIL'))->send($email);
    }
}
