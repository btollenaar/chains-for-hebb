<?php

namespace App\Console\Commands;

use App\Services\EventService;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';

    protected $description = 'Send reminder emails for events happening tomorrow';

    public function handle(EventService $eventService): int
    {
        $count = $eventService->sendReminders();

        $this->info("Sent {$count} event reminder(s).");

        return self::SUCCESS;
    }
}
