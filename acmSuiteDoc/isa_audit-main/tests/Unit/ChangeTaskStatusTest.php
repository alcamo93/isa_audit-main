<?php

namespace Tests\Feature;

use App\Models\V2\Admin\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChangeTaskStatusTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'admin@isaambiental.com')->first();

        $this->actingAs($this->user);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_change_task_status_fail_boolean_approve()
    {
            $response = $this->put(route('library.approve', ['id' => 106]), [
                'approve' => 'x',
                'id_task' => 3274
            ]);

            $response->assertStatus(422);
    }

    public function test_change_task_status_fail_integer_id_task()
    {
            $response = $this->put(route('library.approve', ['id' => 106]), [
                'approve' => 'x',
                'id_task' => 3274
            ]);

            $response->assertStatus(422);
    }

    public function test_change_task_status_fail()
    {
            $response = $this->put(route('library.approve', ['id' => 106]), [
                'approve' => 'x',
                'id_task' => '3274'
            ]);

            $response->assertStatus(422);
    }

    public function test_change_task_status_fail_without_data()
    {
            $response = $this->put(route('library.approve', ['id' => 106]));

            $response->assertStatus(422);
    }

    public function test_change_task_status_success()
    {
            $response = $this->put(route('library.approve', ['id' => 106]), [
                'approve' => true,
                'id_task' => 3274
            ]);

            $response->assertStatus(200);
    }
}
