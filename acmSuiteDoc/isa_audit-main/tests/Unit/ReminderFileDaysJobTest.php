<?php

namespace Tests\Unit;

use App\Classes\HandlerReminderFileDay;
use Tests\TestCase;

class ReminderFileDaysJobTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_reminder_file_days_command()
    {
        $handler = new HandlerReminderFileDay('reminder:file-days');

        $this->assertInstanceOf(HandlerReminderFileDay::class, $handler);
    }
}
