<?php

namespace BeyondCode\HeloLaravel\Tests;

use BeyondCode\HeloLaravel\Mailer;
use BeyondCode\HeloLaravel\Laravel7Mailer;
use BeyondCode\HeloLaravel\TestMail;
use BeyondCode\HeloLaravel\TestMailCommand;
use Illuminate\Support\Facades\Mail;

uses(TestCase::class);

test('the mail commands sends the mailable', function ()
{
    Mail::fake();

    $this->artisan(TestMailCommand::class);

    Mail::assertSent(TestMail::class);
});

test('plain text mails work correctly(', function ()
{
    Mail::fake();

    Mail::to('test@usehelo.com')->send(new TestMail(true));

    Mail::assertSent(TestMail::class);
});

test('the correct mailer is binded', function () {
    $mailer = app(Mailer::class);

    if (version_compare(app()->version(), '7.0.0', '<')) {
        $this->assertTrue($mailer instanceof Mailer);
    } else {
        $this->assertTrue($mailer instanceof Laravel7Mailer);
    }
});
