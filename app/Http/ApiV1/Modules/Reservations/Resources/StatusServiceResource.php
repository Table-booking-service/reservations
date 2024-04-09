<?php

namespace App\Http\ApiV1\Modules\Reservations\Resources;

use App\Domain\Reservations\Models\Reservation;
use App\Http\ApiV1\Support\Resources\BaseJsonResource;

/**
 * @mixin array
 */
class StatusServiceResource extends BaseJsonResource
{
    public function toArray($request): array
    {
        return [
            'data' => $this->resource,
        ];
    }
}
