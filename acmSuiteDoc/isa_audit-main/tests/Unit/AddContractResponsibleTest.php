<?php

namespace Tests\Unit;

use App\Models\V2\Admin\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddContractResponsibleTest extends TestCase
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
    public function test_add_contract_responsible_fail()
    {
        $response = $this->post(route('v2.contract.contact', ['idCorporate' => 24]), [
            'id_user' => 'a'
        ]);

        $response->assertStatus(422);
    }

    public function test_add_contract_responsible_id_user_fail()
    {
        $response = $this->post(route('v2.contract.contact', ['idCorporate' => 24]), [
            'id_user' => 1200
        ]);

        $response->assertStatus(422);
    }

    public function test_add_contract_responsible_id_corporate_not_found()
    {
        $response = $this->post(route('v2.contract.contact', ['idCorporate' => 244]), [
            'id_user' => 120
        ]);

        $response->assertStatus(404);
    }

    public function test_add_contract_responsible_id_corporate_fail()
    {
        $response = $this->post(route('v2.contract.contact', ['idCorporate' => 2]), [
            'id_user' => 120
        ]);

        $response->assertStatus(200);
    }

    public function test_add_contract_responsible_success()
    {
        $response = $this->post(route('v2.contract.contact', ['idCorporate' => 24]), [
            'id_user' => 120
        ]);

        $response->assertStatus(200);
    }
}
