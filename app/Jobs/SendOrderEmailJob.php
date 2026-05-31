<?php

namespace App\Jobs;

use App\Services\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $event,
        public string $email,
        public array $data = [],
    ) {}

    public function handle(EmailSender $emailSender): void
    {
        $emailSender->send($this->event, $this->email, $this->data);
    }
}
