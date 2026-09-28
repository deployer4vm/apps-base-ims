<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StorageSecurityTest extends TestCase
{
    /** @test */
    public function public_images_can_be_served_with_safe_headers()
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

    /** @test */
    public function private_storage_is_not_exposed_to_guests()
    {
        Storage::fake('local');
        Storage::put('private/secret.txt', 'secret');

        $this->assertFalse(Auth::guard('web')->check());
        $this->assertFalse(Auth::guard('api')->check());
        $response = $this->get('/storage/private/secret.txt');

        $response->assertNotFound();
    }

    /** @test */
    public function active_content_in_a_public_image_directory_is_not_exposed()
    {
        Storage::fake('local');
        Storage::put('images/payload.html', '<script>alert(1)</script>');

        $this->get('/storage/images/payload.html')->assertNotFound();
    }

    /** @test */
    public function legacy_public_storage_only_exposes_safe_images_to_guests()
    {
        Storage::fake('public');
        Storage::disk('public')->put('company/logo.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='
        ));
        Storage::disk('public')->put('invoice/attachment/invoice.pdf', '%PDF-1.4');

        $this->get('/public_storage/company/logo.png')->assertOk();
        $this->get('/public_storage/invoice/attachment/invoice.pdf')->assertNotFound();
    }

    /** @test */
    public function editor_endpoints_require_authentication()
    {
        $this->postJson('/api/sys/editor/upload')->assertStatus(401);
        $this->getJson('/api/sys/editor/filemanager')->assertStatus(401);
    }

    /** @test */
    public function missing_acl_rules_are_denied_by_default()
    {
        $userAuth = new \hpsynapse\moduser\Services\UserAuth();

        $this->assertFalse($userAuth->hasAccess('missing.rule', 'r'));
    }

    /** @test */
    public function invoice_validation_rejects_unsigned_urls()
    {
        $this->get('/valid/1')->assertForbidden();
    }

    /** @test */
    public function tenant_database_creation_rejects_unsafe_identifiers()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new \App\Services\Tenant())->createDatabaseSql('1; DROP DATABASE tenants', 0);
    }
}
