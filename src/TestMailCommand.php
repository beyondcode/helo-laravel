<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Console\Command;

class TestMailCommand extends Command
{
    protected $signature = 'helo:test';

    protected $description = 'Send a test mail to your local HELO application.';

    public function handle(\Illuminate\Contracts\Mail\Mailer $mailer)
    {
        $this->info('Sending a test mail to HELO.');

        $mailer->send((new TestMail())->to('test@usehelo.com'));

        $this->info('Check in HELO if it arrived!');
    }
}
