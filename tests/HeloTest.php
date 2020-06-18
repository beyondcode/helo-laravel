<?php

namespace BeyondCode\HeloLaravel\Tests;

use BeyondCode\HeloLaravel\HeloLaravelServiceProvider;
use BeyondCode\HeloLaravel\Mailer;
use BeyondCode\HeloLaravel\Laravel7Mailer;
use BeyondCode\HeloLaravel\TestMail;
use BeyondCode\HeloLaravel\TestMailCommand;
use Illuminate\Support\Facades\Mail;
use Orchestra\Testbench\TestCase;

class HeloTest extends TestCase
{
    protected function getPackageProviders($app)
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

    /** @test */
    public function test_plain_text_mails_work_correctly()
    {
        Mail::fake();

        Mail::to('test@usehelo.com')->send(new TestMail(true));

        Mail::assertSent(TestMail::class);
    }

    /** @test */
    public function test_the_correct_mailer_is_binded()
    {
        $mailer = app(Mailer::class);

        if (version_compare(app()->version(), '7.0.0', '<')) {
            $this->assertTrue($mailer instanceof Mailer);
        } else {
            $this->assertTrue($mailer instanceof Laravel7Mailer);
        }
    }
}
