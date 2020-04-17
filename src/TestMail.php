<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Mail\Mailable;

class TestMail extends Mailable
{
    public function build()
    {
        return $this->subject("Test from HELO")
            ->markdown('helo::email');
    }
}
