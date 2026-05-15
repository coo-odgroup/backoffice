<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BusScheduleCron extends Command
{
    protected $signature = 'busschedule:cron';
    protected $description = 'Generate future bus schedule dates and delete old dates';

    public function handle()
    {
        DB::beginTransaction();

        try {

            $deleteBefore = Carbon::today()->subDays(5)->format('Y-m-d');

            $oldDates = DB::table('odbusdev.bus_schedule_date')
                ->where('entry_date', '<=', $deleteBefore)
                ->get();

            if ($oldDates->count() > 0) {

                $logData = [];

                foreach ($oldDates as $row) {

                    $logData[] = [
                        'bus_schedule_date_id' => $row->id,
                        'bus_schedule_id'      => $row->bus_schedule_id,
                        'entry_date'           => $row->entry_date,
                        'created_at'           => $row->created_at,
                        'created_by'           => $row->created_by,
                        'updated_at'           => $row->updated_at,
                        'updated_by'           => $row->updated_by,
                        'deleted_at'           => now(),
                        'deleted_by'           => 1
                    ];
                }

                DB::table('odbuslog.bus_schedule_date_log')->insert($logData);
            }

            DB::table('odbusdev.bus_schedule_date')
                ->where('entry_date', '<=', $deleteBefore)
                ->delete();



            $seatDeleteBefore = Carbon::today()->subDays(30)->format('Y-m-d');

            DB::table('odbusdev.bus_seat_operation')
                ->whereDate('operation_date', '<=', $seatDeleteBefore)
                ->delete();


            $schedules = DB::table('odbusdev.bus_schedule')
                ->where('active_status', 1)
                ->get();

            $insertData = [];

            foreach ($schedules as $schedule) {

                $busScheduleId = $schedule->id;
                $scheduleType  = strtolower($schedule->schedule_type);

                // limit max 100 schedule dates
                $existingCount = DB::table('odbusdev.bus_schedule_date')
                    ->where('bus_schedule_id', $busScheduleId)
                    ->count();

                if ($existingCount >= 100) {
                    continue;
                }

                $lastDate = DB::table('odbusdev.bus_schedule_date')
                    ->where('bus_schedule_id', $busScheduleId)
                    ->max('entry_date');

                $baseDate = $lastDate
                    ? Carbon::parse($lastDate)
                    : Carbon::today();


                    // Daily schedules

                if ($scheduleType == 'daily') {

                    $runningCycle = (int) $schedule->running_cycle;

                    $gap = ($runningCycle <= 1) ? 1 : $runningCycle;

                    $nextDate = $baseDate
                        ->copy()
                        ->addDays($gap);

                    $date = $nextDate->format('Y-m-d');

                    $exists = DB::table('odbusdev.bus_schedule_date')
                        ->where('bus_schedule_id', $busScheduleId)
                        ->where('entry_date', $date)
                        ->exists();

                    if (!$exists) {

                        $insertData[] = [

                            'bus_schedule_id' => $busScheduleId,
                            'entry_date'      => $date,
                            'created_at'      => now(),
                            'created_by'      => 1

                        ];
                    }
                }


                //Weekly Schedules

                if ($scheduleType == 'weekly') {

                    $weekDays = DB::table('odbusdev.bus_schedule_days')
                        ->where('bus_schedule_id', $busScheduleId)
                        ->pluck('day_number')
                        ->toArray();

                    if (empty($weekDays)) {
                        continue;
                    }

                    // generate next 30 days check
                    for ($i = 1; $i <= 30; $i++) {

                        $nextDate = $baseDate
                            ->copy()
                            ->addDays($i);

                        // Carbon: Sunday=0 ... Saturday=6
                        $carbonDay = $nextDate->dayOfWeek;
                        $mappedDay = ($carbonDay == 0) ? 1 : $carbonDay + 1;

                        if (in_array($mappedDay, $weekDays)) {

                            $date = $nextDate->format('Y-m-d');

                            $exists = DB::table('odbusdev.bus_schedule_date')
                                ->where('bus_schedule_id', $busScheduleId)
                                ->where('entry_date', $date)
                                ->exists();

                            if (!$exists) {

                                $insertData[] = [

                                    'bus_schedule_id' => $busScheduleId,
                                    'entry_date'      => $date,
                                    'created_at'      => now(),
                                    'created_by'      => 1

                                ];

                                break;
                            }
                        }
                    }
                }


                // custom schedules are manually managed
            }

            if (!empty($insertData)) {
                DB::table('odbusdev.bus_schedule_date')->insert($insertData);
            }

            DB::commit();

            $this->info('Bus schedule cron completed successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            $this->error($e->getMessage());
        }
    }
}
