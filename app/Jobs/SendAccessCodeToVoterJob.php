<?php

namespace App\Jobs;

use App\Models\Voter;
use App\Notifications\VoterAccessCodeNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendAccessCodeToVoterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $voterId)
    {
    }

    public function handle(): void
    {
        $voter = Voter::query()->findOrFail($this->voterId);

        Notification::route('mail', $voter->email)
            ->notify(new VoterAccessCodeNotification($voter->access_code, $voter->name));
    }
}