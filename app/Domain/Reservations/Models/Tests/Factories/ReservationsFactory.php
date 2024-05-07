<?php

namespace App\Domain\Reservations\Models\Tests\Factories;

use App\Domain\Reservations\Models\Reservation;
use Ensi\LaravelTestFactories\BaseModelFactory;

class ReservationsFactory extends BaseModelFactory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'table_id' => $this->faker->numberBetween(1, 10),
            'client_id' => $this->faker->numberBetween(1, 10),
            'reservation_start' => '2020-01-01T14:00:00Z', //'2023-08-13T08:44:49Z',
            'duration' => $this->faker->numberBetween(30, 100),
        ];
    }
}
