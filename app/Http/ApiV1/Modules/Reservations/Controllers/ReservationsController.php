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
use Illuminate\Support\Facades\Log;

class ReservationsController
{
    public function getTablesList()
    {
        Log::channel()->info("Starting to get tables list");
        if (!config('x_api_client', false)) {
            Log::channel()->info("Creating x_api_client");
            $client = new Client(['headers' => ['X-Api-Secret' => $this->getXApiSecret()]]);
            config()->set('x_api_client', $client);
        }

        $client = config('x_api_client');
        Log::channel()->info("Getting tables list from " . env("TABLES_SERVICE_IP"));

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection HttpUrlsUsage */
        return $client->get('http://' . env("TABLES_SERVICE_IP") . '/api/v1/tables');
    }

    public function getStatus(): Responsable
    {
        Log::channel()->info("Starting to get status");
        $time = request()->input('reservation_start');
        $reservations = Reservation::query()->where('reservation_start', '<=', $time)
            ->where('reservation_end', '>=', $time)->get();

        if ($reservations->isEmpty()) {
            Log::channel()->info("No reservations found for this time: " . $time);
            abort(404, 'No reservations found for this time.');
        }

        Log::channel()->info("Found " . $reservations->count() . " reservations for this time: " . $time);

        return new StatusServiceResource($reservations);
    }

    public function createReservation(CreateReservationRequest $request): Responsable
    {
        Log::channel()->info("Starting to create reservation");
        if ($this->getReservedTables(
            $request->input('reservation_start'),
            $request->input('duration'),
            $request->input('table_id')
        )->count()) {
            Log::channel()->info("Reservation: " . $request->input('reservation_start') . " for: " . $request->input('table_id') . " overlaps with another reservation");
            abort(400, 'Reservation overlaps with another reservation.');
        }


        $clientId = $request->input('client_id');
        $token = $request->cookie('token');
        $decodedId = openssl_decrypt(
            $token,
            env('X_API_SECRET_ALGORITHM'),
            env('X_API_SECRET_KEY'),
            0,
            str_repeat("0", 16)
        );
        if (!$token || $decodedId != $clientId) {
            Log::channel()->info("Invalid token: " . $token);
            abort(401, $token);
        }

        if (!in_array(
            $request->input('table_id'),
            array_column(
                json_decode($this->getTablesList()->getBody()->getContents(), true)["data"],
                'id'
            )
        )) {
            Log::channel()->info("No such table: " . $request->input('table_id'));
            abort(400, 'No such table.');
        }
        if (!in_array($clientId, array_column($this->getClientsList(), 'id'))) {
            Log::channel()->info("No such client: " . $clientId);
            abort(400, 'No such client.');
        }


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

        Log::channel()->info("Reservation created: " . $request->input('reservation_start') . " for: " . $request->input('table_id'));

        return new ReservationsResource($reservation);
    }

    public function deleteReservation(int $id): Responsable
    {
        Log::channel()->info("Starting to delete reservation: " . $id);
        $reservation = Reservation::query()->find($id);
        $reservation?->delete();

        return new DeleteResponse();
    }

    private function getReservedTables(string $datetimeString, int $duration, int $tableId): Collection
    {
        Log::channel()->info("Starting to get reserved tables: " . $datetimeString . " for: " . $duration . " minutes for table: " . $tableId);
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
        Log::channel()->info("Starting to get X-Api-Secret");
        if (!config('x_api_secret', false)) {
            Log::channel()->info("X-Api-Secret not set, generating");
            $data = env('X_API_SECRET_DATA');
            $algorithm = env('X_API_SECRET_ALGORITHM');
            $key = env('X_API_SECRET_KEY');

            config()->set('x_api_secret', openssl_encrypt($data, $algorithm, $key, 0, str_repeat("0", 16)));
        }

        Log::channel()->info("Got X-Api-Secret: " . config('x_api_secret'));

        return config('x_api_secret');
    }

    private function getClientsList(): array
    {
        Log::channel()->info("Starting to get clients list");
        if (!config('x_api_client', false)) {
            Log::channel()->info("HTTP Client not set, generating");
            $client = new Client(['headers' => ['X-Api-Secret' => $this->getXApiSecret()]]);
            config()->set('x_api_client', $client);
        }

        $client = config('x_api_client');
        Log::channel()->info("Requesting clients list from: " . env("CLIENTS_SERVICE_IP"));
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection HttpUrlsUsage */
        $result = json_decode($client->get('http://' . env("CLIENTS_SERVICE_IP") . '/api/v1/clients')->getBody()->getContents(), true)['data'];

        Log::channel()->info("Got clients list, count: " . count($result));

        return $result;
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
