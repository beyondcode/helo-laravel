<?php

namespace BeyondCode\HeloLaravel\Tests;

use BeyondCode\HeloLaravel\HeloLaravelServiceProvider;
use BeyondCode\HeloLaravel\TestMail;
use BeyondCode\HeloLaravel\TestMailCommand;
use Illuminate\Support\Facades\Mail;
use Orchestra\Testbench\TestCase;

class HeloTest extends TestCase
{
    protected function getPackageProviders()
    {
        return [
            HeloLaravelServiceProvider::class
        ];
    }

    /** @test */
    public function test_the_mail_commands_sends_the_mailable()
    {
        Mail::fake();

        $this->artisan(TestMailCommand::class);

        Mail::assertSent(TestMail::class);
    }
}
