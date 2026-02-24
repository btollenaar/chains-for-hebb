<?php

namespace Tests\Feature;

use App\Jobs\GenerateDataExport;
use App\Models\Customer;
use App\Models\DataExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DataExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_data_export(): void
    {
        Queue::fake();
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer)->post(route('data-export.request'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('data_exports', [
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
        Queue::assertPushed(GenerateDataExport::class);
    }

    public function test_guest_cannot_request_data_export(): void
    {
        $response = $this->post(route('data-export.request'));

        $response->assertRedirect(route('login'));
    }

    public function test_rate_limiting_prevents_multiple_exports(): void
    {
        $customer = Customer::factory()->create();
        DataExport::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($customer)->post(route('data-export.request'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_expired_export_cannot_be_downloaded(): void
    {
        $customer = Customer::factory()->create();
        $export = DataExport::create([
            'customer_id' => $customer->id,
            'status' => 'completed',
            'file_path' => 'data-exports/1/test.zip',
            'requested_at' => now()->subDays(10),
            'completed_at' => now()->subDays(10),
            'expires_at' => now()->subDay(),
        ]);

        $signature = hash_hmac('sha256', $export->id, config('app.key'));
        $response = $this->actingAs($customer)->get(route('data-export.download', ['export' => $export, 'signature' => $signature]));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $customer = Customer::factory()->create();
        $export = DataExport::create([
            'customer_id' => $customer->id,
            'status' => 'completed',
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($customer)->get(route('data-export.download', ['export' => $export, 'signature' => 'invalid']));
        $response->assertForbidden();
    }

    public function test_incomplete_export_cannot_be_downloaded(): void
    {
        $customer = Customer::factory()->create();
        $export = DataExport::create([
            'customer_id' => $customer->id,
            'status' => 'processing',
            'requested_at' => now(),
        ]);

        $signature = hash_hmac('sha256', $export->id, config('app.key'));
        $response = $this->actingAs($customer)->get(route('data-export.download', ['export' => $export, 'signature' => $signature]));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
}
