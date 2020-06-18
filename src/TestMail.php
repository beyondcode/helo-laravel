<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Mail\Mailable;

class TestMail extends Mailable
{
    protected $plainText;

    public function __construct($plainText = false)
    {
        $this->plainText = $plainText;
    }

    public function build()
    {
        $this->subject("Test from HELO");

        return $this->plainText ? $this->text('helo::email') : $this->markdown('helo::email');
    }
}
