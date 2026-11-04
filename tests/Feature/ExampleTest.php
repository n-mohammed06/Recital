<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * L'accueil est réservé aux élèves connectés : un visiteur est envoyé vers la connexion.
     */
    public function test_the_home_page_sends_visitors_to_the_login_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
