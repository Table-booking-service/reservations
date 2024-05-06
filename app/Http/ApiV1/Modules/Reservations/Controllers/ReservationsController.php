<?php

namespace App\Http\ApiV1\Modules\Reservations\Controllers;

use App\Domain\Reservations\Models\Reservation;
use App\Http\ApiV1\Modules\Reservations\Requests\CreateReservationRequest;
use App\Http\ApiV1\Modules\Reservations\Resources\ReservationsResource;
use App\Http\ApiV1\Modules\Reservations\Resources\StatusServiceResource;
use DateTime;
use DateTimeInterface;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ReservationsController
{
    public function getStatus(): Responsable
    {
        $time = request()->input('reservation_start');
        $reservations = Reservation::query()->where('reservation_start', '<=', $time)
            ->where('reservation_end', '>=', $time)->get();

        if ($reservations->isEmpty()) {
            abort(404, 'No reservations found for this time.');
        }

        return new StatusServiceResource($reservations);
    }

    public function createReservation(CreateReservationRequest $request): Responsable
    {
        if ($this->getReservedTables(
            $request->input('reservation_start'),
            $request->input('duration'),
            $request->input('table_id')
        )->count()) {
            abort(400, 'Reservation overlaps with another reservation.');
        }

        $reservation = new Reservation();
        $reservation->table_id = $request->input('table_id');
        $reservation->client_id = $request->input('client_id');
        $reservation->reservation_start = $request->input('reservation_start');
        $reservation->duration = $request->input('duration');
        /** @noinspection PhpUnhandledExceptionInspection */
        $reservation->reservation_end = (
        new DateTime($reservation->reservation_start)
        )
            ->modify("+$reservation->duration minutes")
            ->format(DateTimeInterface::ATOM);
        $reservation->save();

        return new ReservationsResource($reservation);
    }

    public function deleteReservation(int $id): Responsable
    {
        $reservation = Reservation::query()->find($id);
        $reservation?->delete();

        return new DeleteResponse();
    }

    private function getReservedTables(string $datetimeString, int $duration, int $tableId): Collection
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $search_start = new DateTime($datetimeString);
        $search_end = (clone $search_start)->modify("+$duration minutes");

        return Reservation::query()->where('table_id', $tableId)
            ->where(function ($query) use ($search_start, $search_end) {
                $query->where(function ($q) use ($search_start, $search_end) {
                    $q->whereBetween('reservation_start', [$search_start, $search_end])
                        ->orWhereBetween('reservation_end', [$search_start, $search_end])
                        ->orWhere(function ($q) use ($search_start, $search_end) {
                            $q->where('reservation_start', '<=', $search_start)
                                ->where('reservation_end', '>=', $search_end);
                        });
                });
            })
            ->get();
    }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

class DeleteResponse implements Responsable
{
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse();
    }
}
