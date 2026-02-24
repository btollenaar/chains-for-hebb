<?php

namespace Tests\Feature\Admin;

use App\Jobs\ProcessCsvImport;
use App\Models\CsvImport;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->customer = Customer::factory()->create();
    }

    public function test_admin_can_view_imports_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.imports.index'));

        $response->assertOk();
        $response->assertViewIs('admin.imports.index');
        $response->assertViewHas('imports');
        $response->assertViewHas('stats');
    }

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.imports.create'));

        $response->assertOk();
        $response->assertViewIs('admin.imports.create');
    }

    public function test_admin_can_upload_csv(): void
    {
        Queue::fake();
        Storage::fake('local');

        $csvContent = "name,price,sku\nTest Product,19.99,TEST-001\nAnother Product,29.99,TEST-002";
        $file = UploadedFile::fake()->createWithContent('products.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.imports.store'), [
            'file' => $file,
            'type' => 'products',
        ]);

        $this->assertDatabaseHas('csv_imports', [
            'type' => 'products',
            'original_filename' => 'products.csv',
            'total_rows' => 2,
            'uploaded_by' => $this->admin->id,
            'status' => 'pending',
        ]);

        Queue::assertPushed(ProcessCsvImport::class);

        $import = CsvImport::first();
        $response->assertRedirect(route('admin.imports.show', $import));
    }

    public function test_admin_can_view_import_progress(): void
    {
        $import = CsvImport::create([
            'type' => 'products',
            'filename' => 'imports/test.csv',
            'original_filename' => 'test.csv',
            'total_rows' => 50,
            'processed_rows' => 25,
            'successful_rows' => 20,
            'failed_rows' => 5,
            'status' => 'processing',
            'uploaded_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.imports.show', $import));

        $response->assertOk();
        $response->assertViewIs('admin.imports.show');
        $response->assertViewHas('import');
    }

    public function test_progress_endpoint_returns_json(): void
    {
        $import = CsvImport::create([
            'type' => 'products',
            'filename' => 'imports/test.csv',
            'original_filename' => 'test.csv',
            'total_rows' => 100,
            'processed_rows' => 50,
            'successful_rows' => 45,
            'failed_rows' => 5,
            'status' => 'processing',
            'uploaded_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.imports.progress', $import));

        $response->assertOk();
        $response->assertJsonStructure([
            'processed_rows',
            'total_rows',
            'successful_rows',
            'failed_rows',
            'status',
            'progress_percent',
        ]);
        $response->assertJson([
            'processed_rows' => 50,
            'total_rows' => 100,
            'successful_rows' => 45,
            'failed_rows' => 5,
            'status' => 'processing',
            'progress_percent' => 50,
        ]);
    }

    public function test_admin_can_download_template(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.imports.template', 'products'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="products-import-template.csv"');
    }

    public function test_non_admin_cannot_access_imports(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.imports.index'));

        $response->assertForbidden();
    }
}
