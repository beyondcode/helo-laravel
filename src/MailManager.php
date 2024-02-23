<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Factory as FactoryContract;
use Illuminate\Mail\MailManager as LaravelMailManager;

class MailManager extends LaravelMailManager implements FactoryContract
{
    use CreatesMailers;

    public function mailer($name = null)
    {
        if (!$name) {
            return $this->createLaravel9Mailer($this->app);
        }

        return $this->mailers[$name] = $this->get($name);
    }
}
