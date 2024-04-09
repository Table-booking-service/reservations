<?php

namespace App\Domain\Reservations\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $table_id ID столика
 * @property int $client_id ID клиента
 * @property string $reservation_time Время бронирования
 * @property int $duration Длительность бронирования
 *
 * php artisan make:migration
 */
class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'table_id', 'client_id', 'reservation_time', 'duration',
    ];
}
