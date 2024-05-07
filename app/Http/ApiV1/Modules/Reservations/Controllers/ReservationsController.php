<?php

namespace App\Http\ApiV1\Modules\Reservations\Controllers;

use App\Domain\Reservations\Models\Reservation;
use App\Http\ApiV1\Modules\Reservations\Requests\CreateReservationRequest;
use App\Http\ApiV1\Modules\Reservations\Resources\ReservationsResource;
use App\Http\ApiV1\Modules\Reservations\Resources\StatusServiceResource;
use DateTime;
use DateTimeInterface;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ReservationsController
{
    public function getTablesList()
    {
        if (!config('x_api_client', false)) {
            $client = new Client(['headers' => ['X-Api-Secret' => $this->getXApiSecret()]]);
            config()->set('x_api_client', $client);
        }

        $client = config('x_api_client');

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection HttpUrlsUsage */
        return $client->get('http://' . env("TABLES_SERVICE_IP") . '/api/v1/tables');
    }

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


        $clientId = $request->input('client_id');
        //        $token = $request->cookie('token');
        //        $decodedId = openssl_decrypt(
        //            $token,
        //            env('X_API_SECRET_ALGORITHM'),
        //            env('X_API_SECRET_KEY'),
        //            0,
        //            str_repeat("0", 16)
        //        );
        //        if (!$token || $decodedId != $clientId) {
        //            abort(401, $token);
        //        }
        /*
        if (!in_array(
            $request->input('table_id'),
            array_column(
                json_decode($this->getTablesList()->getBody()->getContents()),
                'id'
            )
        )) {
            abort(400, 'No such table.');
        }
        if (!in_array($clientId, array_column($this->getClientsList(), 'id'))) {
            abort(400, 'No such client.');
        }
        */

        $reservation = new Reservation();
        $reservation->table_id = $request->input('table_id');
        $reservation->client_id = $clientId;
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

    private function getXApiSecret(): string
    {
        if (!config('x_api_secret', false)) {
            $data = env('X_API_SECRET_DATA');
            $algorithm = env('X_API_SECRET_ALGORITHM');
            $key = env('X_API_SECRET_KEY');

            config()->set('x_api_secret', openssl_encrypt($data, $algorithm, $key, 0, str_repeat("0", 16)));
        }

        return config('x_api_secret');
    }

    private function getClientsList(): array
    {
        if (!config('x_api_client', false)) {
            $client = new Client(['headers' => ['X-Api-Secret' => $this->getXApiSecret()]]);
            config()->set('x_api_client', $client);
        }

        $client = config('x_api_client');
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection HttpUrlsUsage */
        $result = $client->get('http://' . env("CLIENTS_SERVICE_IP") . '/api/v1/clients');

        return json_decode($result->getBody()->getContents());
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
