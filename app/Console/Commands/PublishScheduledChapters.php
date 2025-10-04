<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Chapter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PublishScheduledChapters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapters:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xuất bản các chương truyện đã được hẹn giờ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Bắt đầu kiểm tra chương cần xuất bản vào: " . $now->format('Y-m-d H:i:s'));

        try {
            $chapterCount = Chapter::where('status', 'draft')
                ->whereNotNull('scheduled_publish_at')
                ->where('scheduled_publish_at', '<=', $now)
                ->count();

            if ($chapterCount == 0) {
                $this->info('Không có chương nào cần xuất bản vào lúc này.');
                return;
            }

            $this->info("Tìm thấy {$chapterCount} chương cần xuất bản.");
            $processedCount = 0;

            Chapter::where('status', 'draft')
                ->whereNotNull('scheduled_publish_at')
                ->where('scheduled_publish_at', '<=', $now)
                ->chunkById(100, function ($chapters) use (&$processedCount) {
                    foreach ($chapters as $chapter) {
                        try {
                            $scheduledTime = $chapter->scheduled_publish_at->format('Y-m-d H:i:s');

                            $chapter->created_at = $chapter->scheduled_publish_at;
                            $chapter->published_at = $chapter->scheduled_publish_at;
                            $chapter->status = 'published';
                            $chapter->save();

                            $processedCount++;

                            $this->info("Đã xuất bản chương {$chapter->number}: {$chapter->title} của truyện ID={$chapter->story_id}");
                            Log::info("Xuất bản tự động: Chương {$chapter->number} '{$chapter->title}' của truyện ID={$chapter->story_id} (theo lịch: {$scheduledTime})");
                        } catch (\Exception $e) {
                            $this->error("Lỗi khi xuất bản chương {$chapter->number}}");
                            Log::error("Lỗi xuất bản tự động: Chương {$chapter->number} - {$e->getMessage()}");
                        }
                    }
                });

            $this->info("Hoàn thành: Đã xuất bản tổng cộng {$processedCount}/{$chapterCount} chương.");

        } catch (\Exception $e) {
            $this->error("Lỗi khi thực hiện xuất bản chương}");
            Log::error("Lỗi xuất bản tự động: {$e->getMessage()}");
        }
    }
}
