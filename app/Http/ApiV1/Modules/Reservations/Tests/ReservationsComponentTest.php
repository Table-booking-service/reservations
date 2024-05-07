<?php

use App\Http\ApiV1\Support\Tests\ApiV1ComponentTestCase;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(ApiV1ComponentTestCase::class);
uses()->group('component');

test('GET /api/v1/tables/status 200', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(200);
});

test('GET /api/v1/tables/status 404', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(404);
});

test('POST /api/v1/reservations 200', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(200);
});

test('POST /api/v1/reservations 404', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(404);
});

test('DELETE /api/v1/reservations/{id} 200', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(200);
});

test('DELETE /api/v1/reservations/{id} 404', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(404);
});

test('GET /api/v1/tables/status 200', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(200);
});

test('GET /api/v1/tables/status 404', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(404);
});

test('POST /api/v1/reservations 200', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(200);
});

test('POST /api/v1/reservations 404', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(404);
});

test('DELETE /api/v1/reservations/{id} 200', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(200);
});

test('DELETE /api/v1/reservations/{id} 404', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(404);
});

test('GET /api/v1/tables/status 200', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(200);
});

test('GET /api/v1/tables/status 404', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(404);
});

test('POST /api/v1/reservations 200', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(200);
});

test('POST /api/v1/reservations 404', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(404);
});

test('DELETE /api/v1/reservations/{id} 200', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(200);
});

test('DELETE /api/v1/reservations/{id} 404', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(404);
});

test('GET /api/v1/tables/status 200', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(200);
});

test('GET /api/v1/tables/status 404', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(404);
});

test('POST /api/v1/reservations 200', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(200);
});

test('POST /api/v1/reservations 404', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(404);
});

test('DELETE /api/v1/reservations/{id} 200', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(200);
});

test('DELETE /api/v1/reservations/{id} 404', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(404);
});

test('GET /api/v1/tables/status 200', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(200);
});

test('GET /api/v1/tables/status 404', function () {
    getJson('/api/v1/tables/status')
        ->assertStatus(404);
});

test('POST /api/v1/reservations 200', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(200);
});

test('POST /api/v1/reservations 404', function () {
    postJson('/api/v1/reservations')
        ->assertStatus(404);
});

test('DELETE /api/v1/reservations/{id} 200', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(200);
});

test('DELETE /api/v1/reservations/{id} 404', function () {
    deleteJson('/api/v1/reservations/{id}')
        ->assertStatus(404);
});
