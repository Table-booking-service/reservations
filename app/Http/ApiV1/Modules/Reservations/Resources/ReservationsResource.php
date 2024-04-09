<?php

namespace App\Http\ApiV1\Modules\Reservations\Resources;

use App\Domain\Reservations\Models\Reservation;
use App\Http\ApiV1\Support\Resources\BaseJsonResource;

/**
 * @mixin Reservation
 */
class ReservationsResource extends BaseJsonResource
{
    public function toArray($request): array
    {
        return [
            'table_id' => $this->table_id,
            'client_id' => $this->client_id,
            'reservation_time' => $this->reservation_time,
            'duration' => $this->duration,
        ];
    }
}
