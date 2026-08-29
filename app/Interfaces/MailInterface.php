<?php

namespace App\Interfaces;

use Illuminate\Mail\Mailable;

interface MailInterface
{
    
    public function send(string $to, Mailable $mailable): void;
    
}
