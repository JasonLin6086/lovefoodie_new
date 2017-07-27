<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ConfirmationEmail extends BeautyMailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $welcome = 'Welcome! '.$this->user->name.',';
        $confirm_url = url("register/confirm/{$this->user->verify_token}");

        //$subject = "\u{2764}".' LoveFoodies '."\u{2764}".'  Confirmation Email';
        //$updated_subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        $subject = '♥ LoveFoodies ♥ Confirmation Email';

        return $this->view('emails.confirmation', compact('welcome', 'confirm_url'))
                ->subject($subject);
    }

}
