<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use App\Models\V2\Admin\User;

class LoadEvidenceImagesTest extends TestCase
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
    public function test_load_files_success()
    {
        $image = UploadedFile::fake()->image('image.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('v2.register.audit.matter.aspect.evaluate.images', [
            'idAuditRegister' => 47,
            'idAuditMatter' => 182,
            'idAuditAspect' => 254,
            'idAuditEvaluate' => 51265
        ]), 
        [
            'file' => [$image, $image2],
        ]);

        $response->assertStatus(200);
    }

    public function test_load_files_format_image_fail()
    {
        $image = UploadedFile::fake()->image('image.bmp');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('v2.register.audit.matter.aspect.evaluate.images', [
            'idAuditRegister' => 47,
            'idAuditMatter' => 182,
            'idAuditAspect' => 254,
            'idAuditEvaluate' => 51265
        ]), 
        [
            'file' => [$image, $image2],
        ]);

        $response->assertStatus(422);
    }

    public function test_load_files_data_fail()
    {
        $image = UploadedFile::fake()->image('image.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $response = $this->post(route('v2.register.audit.matter.aspect.evaluate.images', [
            'idAuditRegister' => 47,
            'idAuditMatter' => 182,
            'idAuditAspect' => 254,
            'idAuditEvaluate' => 0
        ]), 
        [
            'file' => [$image, $image2],
        ]);

        $response->assertStatus(400);
    }

    public function test_load_files_fail()
    {
        $response = $this->post(route('v2.register.audit.matter.aspect.evaluate.images', [
            'idAuditRegister' => 47,
            'idAuditMatter' => 182,
            'idAuditAspect' => 254,
            'idAuditEvaluate' => 0
        ]));

        $response->assertStatus(400);
    }
}
