<?php

namespace App\Http\ApiV1\Modules\Reservations\Controllers;

use App\Http\ApiV1\Modules\Reservations\Requests\CreateReservationRequest;
use App\Http\ApiV1\Modules\Reservations\Requests\DeleteReservationRequest;
use Illuminate\Contracts\Support\Responsable;

class ReservationsController
{
    public function getStatus(string $time): Responsable
    {
        //
    }

    public function createReservation(CreateReservationRequest $request): Responsable
    {
        //
    }

    public function deleteReservation(int $id, DeleteReservationRequest $request): Responsable
    {
        //
    }
}
