<?php

namespace Tests\Unit;

use App\Models\V2\Admin\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'admin@isaambiental.com')->first();

        $this->actingAs($this->user);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_library_validate_dates_out_range()
    {
        $image = UploadedFile::fake()->image('image.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('library.store'), [
            'id_aplicability_register' => 81,
            'id_requirement' => 1025,
            'id_subrequirement' => null,
            'show_library' => false,
            'name' => 'nombre prueba',
            'has_end_date' => 1,
            'id_category' => 2,
            'id_task' => 3272,
            'evaluateable_id' => 3272,
            'evaluateable_type' => 'Task',
            'file' => [$image, $image2],
            'init_date' => '2024-12-01',
            'end_date' => '2024-12-10',
            'days' => 5,
            'notify_days' => [
                '2024-12-01',
                '2024-12-02',
                '2024-12-03',
                '2024-12-05',
                '2024-12-07',
                '2024-12-09',
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_library_validate_days_out_range()
    {
        $image = UploadedFile::fake()->image('image.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('library.store'), [
            'id_aplicability_register' => 81,
            'id_requirement' => 1025,
            'id_subrequirement' => null,
            'show_library' => false,
            'name' => 'nombre prueba',
            'has_end_date' => 1,
            'id_category' => 2,
            'id_task' => 3272,
            'evaluateable_id' => 3272,
            'evaluateable_type' => 'Task',
            'file' => [$image, $image2],
            'init_date' => '2024-12-01',
            'end_date' => '2024-12-10',
            'days' => 11,
            'notify_days' => [
                '2024-12-01',
                '2024-12-02',
                '2024-12-03',
                '2024-12-05',
                '2024-12-07',
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_library_validate_dates_notify_days_out_range()
    {
        $image = UploadedFile::fake()->image('image.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('library.store'), [
            'id_aplicability_register' => 81,
            'id_requirement' => 1025,
            'id_subrequirement' => null,
            'show_library' => false,
            'name' => 'nombre prueba',
            'has_end_date' => 1,
            'id_category' => 2,
            'id_task' => 3272,
            'evaluateable_id' => 3272,
            'evaluateable_type' => 'Task',
            'file' => [$image, $image2],
            'init_date' => '2024-12-01',
            'end_date' => '2024-12-10',
            'days' => 5,
            'notify_days' => [
                '2024-12-01',
                '2024-12-02',
                '2024-12-03',
                '2025-12-09',
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_library_validate_dates_success()
    {
        $image = UploadedFile::fake()->image('image.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('library.store'), [
            'id_aplicability_register' => 81,
            'id_requirement' => 1025,
            'id_subrequirement' => null,
            'show_library' => false,
            'name' => 'nombre prueba',
            'has_end_date' => 1,
            'id_category' => 2,
            'id_task' => 3272,
            'evaluateable_id' => 3272,
            'evaluateable_type' => 'Task',
            'file' => [$image, $image2],
            'init_date' => '2024-12-01',
            'end_date' => '2024-12-10',
            'days' => 5,
            'notify_days' => [
                '2024-12-01',
                '2024-12-03',
                '2024-12-05',
                '2024-12-07',
                '2024-12-09',
            ]
        ]);

        $response->assertStatus(200);
    }
}
