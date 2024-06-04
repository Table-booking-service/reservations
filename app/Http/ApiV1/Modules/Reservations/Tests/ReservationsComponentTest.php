<?php

use App\Domain\Reservations\Models\Reservation;
use App\Http\ApiV1\Support\Tests\ApiV1ComponentTestCase;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

uses(ApiV1ComponentTestCase::class);
uses()->group('component');

test('GET /api/v1/tables 200', function () {
    if (env('TABLES_SERVICE_IP') === '1.1.1.1') {
        $this->assertTrue(true);

        return;
    }

    $this->getJson('/api/v1/tables')
        ->assertStatus(200);
});


test('GET /api/v1/tables/status 404', function () {
    getJson('/api/v1/tables/status?reservation_start=2000-01-02T14:00:00Z')
        ->assertStatus(404);
});


//test('POST /api/v1/reservations 201', function () {
//    $request = [
//        'table_id' => 1,
//        'client_id' => 1,
//        'reservation_start' => '2220-01-01T14:00:00Z',
//        'duration' => 30,
//    ];
//
//    postJson('/api/v1/reservations', $request)
//        ->assertStatus(201);
//});

//test('POST /api/v1/reservations 400', function () {
//    postJson('/api/v1/reservations')
//        ->assertStatus(400);
//});
//
//test('POST /api/v1/reservations 401', function () {
//    postJson('/api/v1/reservations')
//        ->assertStatus(401);
//});
//
//test('POST /api/v1/reservations 404', function () {
//    postJson('/api/v1/reservations')
//        ->assertStatus(404);
//});
//
test('DELETE /api/v1/reservations/{id} 200', function () {
    $reservation = Reservation::factory()->create();

    $intId = intval($reservation->id);
    deleteJson("/api/v1/reservations/{$intId}", ['id' => $intId])
        ->assertStatus(200)
        ->assertJsonPath('data', null);
});

//test('DELETE /api/v1/reservations/{id} 401', function () {
//    deleteJson('/api/v1/reservations/{id}')
//        ->assertStatus(401);
//});
