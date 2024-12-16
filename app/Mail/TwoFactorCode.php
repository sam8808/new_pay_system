<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCode extends Mailable
{
    use SerializesModels;

    public $code; // Store the 2FA code

    /**
     * Create a new message instance.
     *
     * @param string $code
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code; // Assign the 2FA code to the public property
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your 2FA Code')
                    ->view('emails.two_factor_code'); // Specify the email view
    }
}
