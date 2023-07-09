<?php

namespace App\Mail;

use App\Models\Affiliate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AffiliateCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public Affiliate $affiliate
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info("im bit");
        return $this->view('welcome');
    }
}
