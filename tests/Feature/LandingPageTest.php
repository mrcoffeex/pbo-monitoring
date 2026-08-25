<?php

it('renders the public landing page for visitors', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Skip to main content', false);
    $response->assertSee('An official website of the Provincial Government of Davao del Sur', false);
    $response->assertSee('Follow every project from budget to payment', false);
    $response->assertSee('Projects', false);
    $response->assertSee('Budget', false);
    $response->assertSee('Payments', false);
    $response->assertSee('How it works', false);
    $response->assertSee('What you can do', false);
    $response->assertSee('Contact the office', false);
    $response->assertSee('Sign in to the dashboard', false);
    $response->assertSee('pbodavsur@gmail.com', false);
});

it('explains the four project stages in plain language', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Procurement', false);
    $response->assertSee('Obligation', false);
    $response->assertSee('Implementation', false);
    $response->assertSee('Payment', false);
    $response->assertSee('See the whole project', false);
});

it('links staff to the admin sign-in page', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(route('filament.admin.auth.login'), false);
});
