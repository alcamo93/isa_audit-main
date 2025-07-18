<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateZipCompiledDocuments;
use App\Models\V2\Admin\User;

class DownloadCompiledDocumentsTest extends TestCase
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
    public function test_download_zip_documents_success()
    {
        $data = ['some' => 'data']; 

        Queue::fake();

        $user = $this->user;

        GenerateZipCompiledDocuments::dispatch($user, $data);

        Queue::assertPushed(GenerateZipCompiledDocuments::class, function ($job) use ($user, $data) {
            return $job->userId === $user && $job->data === $data;
        });

    }

    public function test_download_zip_documents_error()
    {
        $mockedService = $this->mock(SomeExternalService::class, function ($mock) {
            $mock->shouldReceive('processDocuments')
                ->once()
                ->andThrow(new \Exception('Error en el proceso del archivo zip'));
        });

        $user = $this->user;
        $data = ['some' => 'data'];
        Queue::fake();
        
        GenerateZipCompiledDocuments::dispatch($user, $data);

        Queue::assertPushed(GenerateZipCompiledDocuments::class, function ($job) use ($user, $data) {
            return $job->userId === $user && $job->data === $data;
        });

        try {
            $mockedService->processDocuments();
        } catch (\Exception $e) {
            $this->assertEquals('Error en el proceso del archivo zip', $e->getMessage());
        }
    }
}
