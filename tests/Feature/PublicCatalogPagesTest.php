<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_pages_are_accessible_without_login(): void
    {
        $this->get('/produk')->assertOk();
        $this->get('/limbah')->assertOk();
        $this->get('/edukasi')->assertOk();
    }

    public function test_checkout_success_view_exists(): void
    {
        $this->assertTrue(view()->exists('user.checkout.success'));
    }
}
