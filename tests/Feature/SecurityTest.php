<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function test_pages_carry_the_security_headers(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/connexion');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'same-origin');
        $this->assertStringContainsString("script-src 'self'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_https_is_recognised_behind_the_host_proxy(): void
    {
        config(['trustedproxy.proxies' => '*']);

        $this->get('/connexion', ['X-Forwarded-Proto' => 'https'])
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_the_real_visitor_address_is_used_behind_the_host_proxy(): void
    {
        config(['trustedproxy.real_ip_header' => 'X-Real-IP']);

        // Une adresse invalide est ignorée.
        $this->get('/connexion', ['X-Real-IP' => 'pas une adresse'])->assertOk();
        $this->assertSame('127.0.0.1', request()->ip());

        $this->get('/connexion', ['X-Real-IP' => '203.0.113.7'])->assertOk();
        $this->assertSame('203.0.113.7', request()->ip());
    }

    public function test_an_unknown_page_shows_the_french_error_page(): void
    {
        $this->get('/page-qui-n-existe-pas')
            ->assertNotFound()
            ->assertSee('Page introuvable');
    }
}
