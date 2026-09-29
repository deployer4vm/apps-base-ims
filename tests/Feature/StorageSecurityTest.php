<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Production deployments may run this suite while the application is
        // intentionally in maintenance mode. Bypass only that middleware so
        // these tests still exercise the storage and authentication controls.
        $this->withoutMiddleware(\App\Http\Middleware\CheckForMaintenanceMode::class);
    }

    public function test_public_images_are_served_with_safe_headers(): void
    {
        Storage::fake('local');
        Storage::put('images/pixel.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='
        ));

        $response = $this->get('/storage/images/pixel.png');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Content-Security-Policy', "default-src 'none'; sandbox");
    }

    public function test_private_storage_is_not_exposed_to_guests(): void
    {
        Storage::fake('local');
        Storage::put('private/secret.txt', 'secret');

        $this->assertFalse(Auth::guard('web')->check());
        $this->assertFalse(Auth::guard('api')->check());
        $this->get('/storage/private/secret.txt')->assertNotFound();
    }

    public function test_active_content_in_public_image_directory_is_not_exposed(): void
    {
        Storage::fake('local');
        Storage::put('images/payload.html', '<script>alert(1)</script>');

        $this->get('/storage/images/payload.html')->assertNotFound();
    }

    public function test_all_tenant_content_only_serves_safe_images(): void
    {
        Storage::fake('alltenant');
        Storage::disk('alltenant')->put('content/pixel.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='
        ));
        Storage::disk('alltenant')->put('content/payload.html', '<script>alert(1)</script>');

        $this->get('/storage/content/pixel.png')->assertOk();
        $this->get('/storage/content/payload.html')->assertNotFound();
    }

    public function test_editor_endpoints_require_authentication(): void
    {
        $this->postJson('/api/sys/editor/upload')->assertUnauthorized();
        $this->getJson('/api/sys/editor/filemanager')->assertUnauthorized();
    }

    public function test_tenant_database_creation_rejects_unsafe_identifiers(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new \App\Services\Tenant())->createDatabaseSql(
            '1; DROP DATABASE tenants',
            0
        );
    }
}
