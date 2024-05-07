<?php

namespace App\Domain\Reservations\Models;

use App\Domain\Reservations\Models\Tests\Factories\ReservationsFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $table_id ID столика
 * @property int $client_id ID клиента
 * @property string $reservation_start Время бронирования
 * @property int $duration Длительность бронирования
 * @property string $reservation_end Время окончания бронирования
 *
 * php artisan make:migration
 */
class Reservation extends Model
{
    public $timestamps = false;
    protected $table = 'reservations';

    protected $fillable = [
        'table_id', 'client_id', 'reservation_start', 'duration',
    ];

    public static function factory(): ReservationsFactory
    {
        return ReservationsFactory::new();
    }
}
