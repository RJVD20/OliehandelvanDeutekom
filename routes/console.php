<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('payments:send-reminders')->dailyAt('09:00');
Schedule::command('payments:expire-open')->dailyAt('00:30');
Schedule::command('newsletter:dispatch-scheduled')->everyMinute();
