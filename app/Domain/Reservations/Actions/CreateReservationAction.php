<?php

namespace App\Domain\Reservations\Actions;

use App\Domain\Reservations\Models\Reservation;

class CreateReservationAction
{
    public function execute(array $fields): Reservation
    {
        $reservation = new Reservation();
        $reservation->fill($fields);
        $reservation->save();

        return $reservation;
    }
}
