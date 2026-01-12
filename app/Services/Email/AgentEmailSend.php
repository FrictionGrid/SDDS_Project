<?php

namespace App\Services\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentEmailSend extends Mailable
{
    use Queueable, SerializesModels;

    public string $bodyText;

    public function __construct(
        public string $subjectText,
        string $body
    ) {
        $this->bodyText = $body;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
            ->text('emails.agent_plain');
    }
}
