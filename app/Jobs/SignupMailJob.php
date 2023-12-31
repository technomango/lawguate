<?php

namespace App\Jobs;

use Mail;
use App\Mail\SignupMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Setting\Model\EmailTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SignupMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $template = EmailTemplate::where('type', 'sign_up_email')->where('status', 1)->first();
        if ($template) {
            Mail::to($this->user['email'])->send(new SignupMail($this->user, $template));
        }
    }
}
