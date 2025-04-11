<?php

namespace App\Console\Commands;

use App\Models\UserActivity;
use App\Models\UserPoint;
use Illuminate\Console\Command;

class Recalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $totals = UserActivity::selectRaw('user_id, SUM(points) as total_points')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get();

        $rank = 1;
        $lastPoints = null;

        foreach ($totals as $index => $userData) {
            if ($userData->total_points !== $lastPoints) {
                $rank = $index === 0 ? 1 : $rank + 1;
            }
            UserPoint::updateOrCreate(
                ['user_id' => $userData->user_id],
                [
                    'total_points' => $userData->total_points,
                    'rank' => $rank
                ]
            );
            $lastPoints = $userData->total_points;
        }
        $this->info('User points updated!');
    }
}
