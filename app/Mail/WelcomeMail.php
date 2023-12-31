<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Setting\Model\EmailTemplate;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    private $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $template)
    {
        $this->user = $user;
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tamplate = $this->template;

        $subject= $tamplate->subject;
        $body = $tamplate->value;
        $key = ['%7B', '%7D'];
        $value = ['{', '}'];
        $body = str_replace($key, $value, $body);
        $key = ['{USER_NAME}','{APP_NAME}','{EMAIL_SIGNATURE}'];
        $value = [$this->user['name'],config('configs.site_title'),config('configs.mail_signature')];
        $body = str_replace($key, $value, $body);


        return $this->view('mail_body')->with(["body" => $body])->subject($subject);
    }
}
