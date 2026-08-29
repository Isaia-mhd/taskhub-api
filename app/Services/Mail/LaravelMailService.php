<?php

namespace App\Services\Mail;

use App\Interfaces\MailInterface;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class LaravelMailService implements MailInterface
{
    public function send(string $to, Mailable $mailable): void
    {
        Mail::to($to)->send($mailable);
    }
}
