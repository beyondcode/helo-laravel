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
}
