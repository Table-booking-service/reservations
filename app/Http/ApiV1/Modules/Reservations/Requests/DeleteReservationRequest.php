<?php

namespace App\Http\ApiV1\Modules\Reservations\Requests;

use App\Http\ApiV1\Support\Requests\BaseFormRequest;

class DeleteReservationRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|min:1',
        ];
    }
}
