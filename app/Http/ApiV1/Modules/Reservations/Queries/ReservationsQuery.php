<?php

namespace App\Http\ApiV1\Modules\Reservations\Queries;

use App\Domain\Reservations\Models\Reservation;
use Spatie\QueryBuilder\QueryBuilder;

class ReservationsQuery extends QueryBuilder
{
    public function __construct()
    {
        // Связь с моделью
        parent::__construct(Reservation::query());

        // Разрешить сортировать по параметрам
        $this->allowedSorts(['id', 'table_id']);

        // Сортировка по умолчанию
        $this->defaultSort('-id');
    }
}
