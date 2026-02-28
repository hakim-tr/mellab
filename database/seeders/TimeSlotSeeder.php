<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    public function run()
    {
        $field = Field::first();
        if (!$field) {
            $field = Field::create([
                'name' => 'ملعب كرة القدم',
                'location' => 'الحي الرياضي',
                'price_per_hour' => 100,
            ]);
        }

        // كل ساعة من 06:00 حتى 00:00
        $startTimes = [];
        for ($h = 6; $h <= 24; $h++) {
            $hour = str_pad($h % 24, 2, '0', STR_PAD_LEFT); // 24 => 00
            $startTimes[] = "$hour:00";
        }

        // لمدة سنة كاملة
        foreach(range(0, 364) as $dayOffset) {
            $date = now()->addDays($dayOffset)->toDateString();
            foreach($startTimes as $start){
                $end = date('H:i', strtotime($start) + 60*60); // ساعة
                TimeSlot::firstOrCreate([
                    'field_id' => $field->id,
                    'date' => $date,
                    'start_time' => $start,
                ], [
                    'end_time' => $end,
                    'is_available' => true,
                ]);
            }
        }
    }
}