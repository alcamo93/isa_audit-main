<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Notifications\RenewalContractNotification;
use App\Models\V2\Admin\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Mockery;

class ContractRenewalNotificationTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_contract_renewal_notification_send_email_success()
    {
        Notification::fake();

        $contractData = [
            'fullname' => 'Usuario Corporativo',
            'contract' => 'PRUEBA - CONTRACT',
            'start_date' => '06/01/2025',
            'end_date' => '02/10/2025',
            'days' => '27',
            'customer' => [
                'cust_tradename' => 'EJEMPLO'
            ],
            'corporate' => [
                'corp_tradename' => 'EJEMPLO CORP'
            ],
            'license' => [
                'name' => 'PRUEBA'
            ],
        ];

        $user = User::find(119);

        if (!$user) {
            $this->fail('Usuario no encontrado.');
        }

        $notification = new RenewalContractNotification($contractData);

        $user->notify($notification);

        Notification::assertSentTo(
            [$user], RenewalContractNotification::class, function ($notification, $channels) use ($contractData) {
                return in_array('mail', $channels) &&
                    $notification->notifyData === $contractData;
            }
        );
    }

    public function test_contract_renewal_notification_send_email_fail()
    {
        Mail::fake();

        $contractData = [
            'fullname' => 'Usuario Corporativo',
            'contract' => 'PRUEBA - CONTRACT',
            'start_date' => '06/01/2025',
            'end_date' => '02/10/2025',
            'days' => '27',
            'customer' => [
                'cust_tradename' => 'EJEMPLO'
            ],
            'corporate' => [
                'corp_tradename' => 'EJEMPLO CORP'
            ],
            'license' => [
                'name' => 'PRUEBA'
            ],
        ];

        $user = User::find(119);

        if (!$user) {
            $this->fail('Usuario no encontrado.');
        }

        $notification = new RenewalContractNotification($contractData);

        $this->app->bind('mailer', function ($app) {
            $mailerMock = Mockery::mock(\Illuminate\Mail\Mailer::class);
            $mailerMock->shouldReceive('send')->andThrow(new \Exception('Error en el envío del correo'));
            return $mailerMock;
        });

        try {
            $user->notify($notification);
        } catch (\Exception $e) {
            $this->assertStringContainsString('Error en el envío del correo', $e->getMessage());
        }

        Mail::assertNotSent(RenewalContractNotification::class);
    }
}
