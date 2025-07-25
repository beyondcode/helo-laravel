<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

class GeneralLaravelMailer extends Mailer implements MailerContract, MailFactory
{
    public $currentMailer = null;
    protected $app;
    protected $mailers = [];

    public function mailer($name = null)
    {
        $this->currentMailer = $name;

        return $this;
    }

    /**
     * Set laravel application.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     */
    public function setApplication($app)
    {
        $this->app = $app;
    }

    /**
     * Forget all of the resolved mailer instances.
     *
     * @return $this
     */
    public function forgetMailers()
    {
        $this->mailers = [];

        return $this;
    }
}
