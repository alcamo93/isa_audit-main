<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\V2\Admin\User;
use Illuminate\Http\UploadedFile;

class NewsTest extends TestCase
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
    public function test_add_news_fail()
    {
        $image = UploadedFile::fake()->image('image.jpg');

        $data = [
            // 'headline' => 'titular prueba',
            'file' => $image,
            'topics' => [1, 3],
            'description' => '<p>Descripción prueba</p>',
            'publication_start_date' => '2025-02-21',
            'publication_closing_date' => '2025-02-24',
            'historical_start_date' => '2025-02-25',
            'historical_closing_date' => '2025-02-28',
            'published' => false
        ];

        $response = $this->post(route('v2.new.store'), $data);

        $response->assertStatus(422);
    }

    public function test_add_news_dates_fail()
    {
        $image = UploadedFile::fake()->image('image.jpg');

        $data = [
            'headline' => 'titular prueba',
            'file' => $image,
            'topics' => [1, 3],
            'description' => '<p>Descripción prueba</p>',
            'publication_start_date' => '2025-02-25',
            'publication_closing_date' => '2025-02-24',
            'historical_start_date' => '2025-02-25',
            'historical_closing_date' => '2025-02-28',
            'published' => false
        ];

        $response = $this->post(route('v2.new.store'), $data);

        $response->assertStatus(422);
    }

    public function test_add_news_success()
    {
        $image = UploadedFile::fake()->image('image.jpg');

        $data = [
            'headline' => 'titular prueba desde test',
            'file' => $image,
            'topics' => [1, 3],
            'description' => '<p>Descripción prueba</p>',
            'publication_start_date' => '2025-02-21',
            'publication_closing_date' => '2025-02-24',
			'historical_start_date' => '2025-02-25',
			'historical_closing_date' => '2025-02-28',
            'published' => false
        ];

        $response = $this->post(route('v2.new.store'), $data);

        $response->assertStatus(200);
    }

    public function test_update_news_fail() 
    {
        $newsId = 0;
        $image = UploadedFile::fake()->image('new-image.jpg');

        $data = [
            'headline' => 'titular actualizado desde test',
            'file' => $image,
            'topics' => [1, 2], 
            'description' => '<p>Descripción actualizada</p>',
            'publication_start_date' => '2025-03-01',
            'publication_closing_date' => '2025-03-05',
            'historical_start_date' => '2025-03-06',
            'historical_closing_date' => '2025-03-10',
            'published' => true
        ];

        $response = $this->post(route('v2.new.update', ['id' => $newsId]), $data);

        $response->assertStatus(404);
    }

    public function test_update_news_success() 
    {
        $newsId = 13;

        $data = [
            'headline' => 'titular actualizado desde test',
            'file' => null,
            'topics' => [1, 2], 
            'description' => '<p>Descripción prueba actualizada desde test</p>',
            'publication_start_date' => '2025-03-01',
            'publication_closing_date' => '2025-03-05',
            'historical_start_date' => '2025-03-06',
            'historical_closing_date' => '2025-03-10',
            'published' => false
        ];

        $response = $this->post(route('v2.new.update', ['id' => $newsId]), $data);

        $response->assertStatus(200);
    }

    public function test_update_status_fail()
    {
        $newsId = 3;
        $statusId = 'x';

        $response = $this->put(route('v2.new.status', ['id_status' => $statusId, 'id' => $newsId]));

        $response->assertStatus(422);

    }

    public function test_update_status_success()
    {
        $newsId = 3;
        $statusId = true;

        $response = $this->put(route('v2.new.status', ['id_status' => $statusId, 'id' => $newsId]));

        $response->assertStatus(200);

    }

    public function test_delete_news_success() 
    {
        $newsId = 4;

        $response = $this->delete(route('v2.new.destroy', ['id' => $newsId]));

        $response->assertStatus(200);
    }
}
