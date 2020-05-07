<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

class Laravel7Mailer extends Mailer implements MailerContract, MailFactory
{
    public function mailer($name = null)
    {
        $this->currentMailer = $name;

        return $this;
    }
    
     /**
     * Void function, bug fix for BeyondCode Hello
     * Github issue: https://github.com/beyondcode/helo-laravel/issues/15
     */
    public static function extend()
    {

    }
}
