<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Classes\HandlerReminderContracts;

class ReminderContractExpirationsJobTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_reminder_contract_expirations_command()
    {
        $handler = new HandlerReminderContracts('reminder:contracts');

        $this->assertInstanceOf(HandlerReminderContracts::class, $handler);
    }
}
