<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Ticker extends Mailable
{
    use Queueable, SerializesModels;

    private $oldTicker;
    private $newTicker;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Models\Ticker $oldTicker, array $newTicker)
    {
        $this->oldTicker = $oldTicker;
        $this->newTicker = $newTicker;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('toaran@genacrys.com')->subject($this->oldTicker->pair)->view('mails.ticker')->with([
            'old' => $this->oldTicker,
            'new' => $this->newTicker
        ]);
    }
}
