<?php

namespace App\Http\ApiV1\Modules\Reservations\Requests;

use App\Http\ApiV1\Support\Requests\BaseFormRequest;

class CreateReservationRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'duration' => ['required', 'integer'],
            'reservation_time' => ['required', 'string'],
            'client_id' => ['required', 'integer'],
            'table_id' => ['required', 'integer'],
        ];
    }
}
