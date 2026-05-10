<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteAndApiSmokeTest extends TestCase
{
    public function test_changed_named_routes_are_registered(): void
    {
        $routeNames = [
            'contactDetails',
            'vehicleSettings',
            'cardDetails',
            'showCardDetail',
            'paymentMethodShow',
            'paymentMethodUpdate',
            'paymentMethodDelete',
            'drivers.verifications.index',
            'drivers.verifications.show',
            'drivers.verifications.update',
            'notification-settings.show',
            'notification-settings.update',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertTrue(Route::has($routeName), "Missing route: {$routeName}");
        }
    }

    public function test_public_web_routes_smoke(): void
    {
        $this->get('/')->assertStatus(302);
        $this->get('/contact-us')->assertStatus(200);
        $this->get('/privacy-policy')->assertStatus(200);
    }

    public function test_guest_access_to_driver_verification_routes_is_blocked(): void
    {
        $this->get('/drivers/verifications')->assertStatus(302);
        $this->get('/drivers/verifications/1')->assertStatus(302);
        $this->patch('/drivers/verifications/1', [
            'verification_status' => 'verified',
        ])->assertStatus(302);
    }

    public function test_protected_api_routes_reject_guests(): void
    {
        $requests = [
            ['GET', '/api/contactDetails', []],
            ['GET', '/api/vehicleSettings', []],
            ['GET', '/api/cardDetails', []],
            ['GET', '/api/cardDetails/1', []],
            ['GET', '/api/paymentMethodList', []],
            ['GET', '/api/paymentMethods/1', []],
            ['GET', '/api/notification-settings', []],
            ['POST', '/api/notification-settings', ['messages' => true]],
        ];

        foreach ($requests as [$method, $uri, $payload]) {
            $response = $this->json($method, $uri, $payload);
            $response->assertStatus(401);
        }
    }

    public function test_public_api_routes_smoke(): void
    {
        $this->getJson('/api/bankList')->assertStatus(200);
        $this->getJson('/api/cities')->assertStatus(200);
        $this->getJson('/api/getBanners')->assertStatus(200);
        $this->getJson('/api/allCategories')->assertStatus(200);
    }
}