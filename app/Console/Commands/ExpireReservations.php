<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservations:expire';

    protected $description = 'Expires pending reservations that have exceeded their expiration time';

    public function handle()
    {
        $this->info('开始检查过期的预订...');

        $expiredReservations = Reservation::where('status', Reservation::STATUS_PENDING)
            ->where('expires_at', '<', Carbon::now())
            ->get();

        $count = 0;

        foreach ($expiredReservations as $reservation) {
            // Update reservation status to expired
            $reservation->update(['status' => Reservation::STATUS_EXPIRED]);

            // Make time slot available again
            $timeSlot = TimeSlot::find($reservation->time_slot_id);
            if ($timeSlot) {
                $timeSlot->update(['is_available' => true]);
            }

            $count++;
            $this->line("预订 ID {$reservation->id} 已过期");
        }

        if ($count > 0) {
            $this->info("已过期 {$count} 个预订");
        } else {
            $this->info('没有过期的预订');
        }

        return Command::SUCCESS;
    }
}
