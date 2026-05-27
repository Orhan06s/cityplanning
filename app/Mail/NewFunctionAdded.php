<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewFunctionAdded extends Mailable
{
    use Queueable, SerializesModels;

    public string $functionName;
    public string $category;
    public ?string $description;
    public string $icon;

    public function __construct(string $functionName, string $category, ?string $description, string $icon)
    {
        $this->functionName  = $functionName;
        $this->category      = $category;
        $this->description   = $description;
        $this->icon          = $icon;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nieuwe functie toegevoegd: ' . $this->functionName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-function-added',
        );
    }
}
