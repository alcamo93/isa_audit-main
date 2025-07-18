<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskChangeStatus;
use App\Models\V2\Admin\User;
use Illuminate\Support\Facades\Mail;
use Mockery;

class ChangeTaskStatusNotificationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_change_task_status_notification_send_email_success()
    {
        Notification::fake();

        $taskData = [
            'approve' => 1,
            'corp_tradename' => 'EJEMPLO',
            'audit_processes' => 'PRUEBA - ACM SUITE',
            'init_date_format' => '06/01/2025',
            'end_date_format' => '02/10/2025',
            'no_requirement' => 'AMB-IA-NL-01',
            'requirement' => 'Autorización en Materia de Impacto Ambientale',
            'close_date' => '31/01/2025',
            'status' => 'Aprobado',
            'days' => '27',
            'title' => 'Título',
            'task' => 'Tarea',
            'id_action_plan' => 1,
            'auditors' => [
                [
                    'person' => [
                        'full_name' => 'Usuario Corporativo'
                    ]
                ]
            ],
        ];

        $user = User::find(129);

        if (!$user) {
            $this->fail('Usuario no encontrado.');
        }

        $notification = new TaskChangeStatus($taskData);

        $user->notify($notification);

        Notification::assertSentTo(
            [$user], TaskChangeStatus::class, function ($notification, $channels) use ($taskData) {
                return in_array('mail', $channels) &&
                    $notification->taskData === $taskData;
            }
        );
    }

    public function test_change_task_status_notification_send_email_fail()
    {
        Mail::fake();

        $taskData = [
            'approve' => 1,
            'corp_tradename' => 'EJEMPLO',
            'audit_processes' => 'PRUEBA - ACM SUITE',
            'init_date_format' => '06/01/2025',
            'end_date_format' => '02/10/2025',
            'no_requirement' => 'AMB-IA-NL-01',
            'requirement' => 'Autorización en Materia de Impacto Ambiental',
            'close_date' => '31/01/2025',
            'status' => 'Aprobado',
            'days' => '27',
            'title' => 'Título',
            'task' => 'Tarea',
            'id_action_plan' => 1,
            'auditors' => [
                [
                    'person' => [
                        'full_name' => 'Usuario Corporativo'
                    ]
                ]
            ], 
        ];

        $user = User::find(129);

        if (!$user) {
            $this->fail('Usuario no encontrado.');
        }

        $notification = new TaskChangeStatus($taskData);

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

        Mail::assertNotSent(TaskChangeStatus::class);
    }
}
