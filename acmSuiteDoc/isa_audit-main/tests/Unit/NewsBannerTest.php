<?php

namespace Tests\Unit;
use Tests\TestCase;
use App\Models\V2\Admin\User;

class NewsBannerTest extends TestCase
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
    public function test_news_index_with_filters_and_pagination()
    {
        $params = [
            'perPage' => 15,
            'page' => 1,
            'filters[banner]' => true,
        ];

        $response = $this->get(route('v2.new.index', $params));

        $response->assertStatus(200);
    }
}
