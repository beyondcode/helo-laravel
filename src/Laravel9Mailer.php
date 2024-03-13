<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

class Laravel9Mailer extends Laravel8Mailer implements MailerContract, MailFactory
{
}
