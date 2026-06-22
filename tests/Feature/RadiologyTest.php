<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\RadiologyResult;
use App\Models\User;
use App\Mail\RadiologyChatMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RadiologyTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_access_radiology_index(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $response = $this->actingAs($operator)->get('/radiology');

        $response->assertOk();
    }

    public function test_dokter_can_access_radiology_index(): void
    {
        $dokter = User::factory()->create(['role' => 'dokter']);

        $response = $this->actingAs($dokter)->get('/radiology');

        $response->assertOk();
    }

    public function test_send_chat_message_email_triggers_mail_send(): void
    {
        Mail::fake();

        $operator = User::factory()->create(['role' => 'operator']);
        
        $patient = Patient::create([
            'patient_code' => 'P001',
            'name' => 'John Doe',
            'email' => 'patient@example.com',
            'phone' => '08123456789',
            'address' => 'Lhokseumawe',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'created_by' => $operator->id,
        ]);
        
        $radiologyResult = RadiologyResult::create([
            'patient_id' => $patient->id,
            'operator_id' => $operator->id,
            'image_path' => 'radiology/images/dummy.png',
            'status' => 'pending',
            'share_token' => 'test-token',
        ]);

        $response = $this->actingAs($operator)
            ->postJson("/radiology/chat/{$radiologyResult->id}/send", [
                'message_text' => 'Hello Patient, here is your scan update.',
                'channel' => 'email',
            ]);

        $response->assertJson([
            'success' => true,
        ]);

        Mail::assertSent(RadiologyChatMessageMail::class, function ($mail) use ($patient) {
            return $mail->hasTo($patient->email) && 
                   $mail->messageText === 'Hello Patient, here is your scan update.';
        });
    }

    public function test_send_chat_message_whatsapp_returns_redirect_url(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        
        $patient = Patient::create([
            'patient_code' => 'P002',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '08123456789',
            'address' => 'Lhokseumawe',
            'date_of_birth' => '1992-02-02',
            'gender' => 'female',
            'created_by' => $operator->id,
        ]);
        
        $radiologyResult = RadiologyResult::create([
            'patient_id' => $patient->id,
            'operator_id' => $operator->id,
            'image_path' => 'radiology/images/dummy2.png',
            'status' => 'pending',
            'share_token' => 'test-token',
        ]);

        $response = $this->actingAs($operator)
            ->postJson("/radiology/chat/{$radiologyResult->id}/send", [
                'message_text' => 'Hello Patient, WhatsApp text.',
                'channel' => 'whatsapp',
            ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertArrayHasKey('whatsapp_url', $response->json());
        $this->assertStringContainsString('628123456789', $response->json()['whatsapp_url']);
    }

    public function test_public_report_portal_accessible(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        
        $patient = Patient::create([
            'patient_code' => 'P003',
            'name' => 'Bob Doe',
            'email' => 'bob@example.com',
            'phone' => '08123456789',
            'address' => 'Lhokseumawe',
            'date_of_birth' => '1995-05-05',
            'gender' => 'male',
            'created_by' => $operator->id,
        ]);
        
        $radiologyResult = RadiologyResult::create([
            'patient_id' => $patient->id,
            'operator_id' => $operator->id,
            'image_path' => 'radiology/images/dummy3.png',
            'status' => 'completed',
            'share_token' => 'bob-token',
        ]);

        $response = $this->get("/report/radiology/bob-token");

        $response->assertOk();
        $response->assertSee('Bob Doe');
        $response->assertSee('Unduh PDF Laporan Resmi');
    }

    public function test_public_report_pdf_download(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        
        $patient = Patient::create([
            'patient_code' => 'P004',
            'name' => 'Alice Doe',
            'email' => 'alice@example.com',
            'phone' => '08123456789',
            'address' => 'Lhokseumawe',
            'date_of_birth' => '1998-08-08',
            'gender' => 'female',
            'created_by' => $operator->id,
        ]);
        
        $radiologyResult = RadiologyResult::create([
            'patient_id' => $patient->id,
            'operator_id' => $operator->id,
            'image_path' => 'radiology/images/dummy4.png',
            'status' => 'completed',
            'share_token' => 'alice-token',
        ]);

        $response = $this->get("/report/radiology/alice-token/pdf");

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'inline; filename=laporan_radiologi_alice_doe.pdf');
    }
}
