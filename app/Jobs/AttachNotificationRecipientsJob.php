<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AttachNotificationRecipientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $notificationId,
        public array $userIds
    ) {}

    public function handle(): void
    {
        $notification = Notification::find($this->notificationId);
        if (!$notification || $notification->is_broadcast) {
            return;
        }

        $chunkSize = 500;
        foreach (array_chunk($this->userIds, $chunkSize) as $chunk) {
            $rows = collect($chunk)->map(fn ($userId) => [
                'notification_id' => $this->notificationId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            DB::table('notification_user')->insertOrIgnore($rows);
        }
    }
}
