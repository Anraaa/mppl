<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Helper\EncryptionHelper;

/**
 * @OA\Tag(
 *     name="Bookings",
 *     description="Manajemen Booking Studio"
 * )
 */
class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/bookings",
     *     operationId="getBookings",
     *     tags={"Bookings"},
     *     summary="Get all bookings",
     *     description="Returns a list of all bookings",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="encrypted string")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = Booking::all();

        $response = [
            'message' => 'success',
            'data' => $data
        ];

        $encrypted = EncryptionHelper::encrypt(json_encode($response));

        return response()->json(['data' => $encrypted]);
    }

    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     operationId="createBooking",
     *     tags={"Bookings"},
     *     summary="Create a new booking",
     *     description="Stores a new booking",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "studio_id", "booking_date", "start_time", "end_time"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="studio_id", type="integer", example=1),
     *             @OA\Property(property="booking_date", type="string", format="date", example="2025-07-21"),
     *             @OA\Property(property="start_time", type="string", example="10:00"),
     *             @OA\Property(property="end_time", type="string", example="12:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking created",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="encrypted string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'studio_id' => 'required|integer',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        $booking = Booking::create($validated);

        $response = [
            'message' => 'Booking created successfully',
            'data' => $booking
        ];

        $encrypted = EncryptionHelper::encrypt(json_encode($response));

        return response()->json(['data' => $encrypted]);
    }

    /**
     * @OA\Get(
     *     path="/api/bookings/{id}",
     *     operationId="getBookingById",
     *     tags={"Bookings"},
     *     summary="Get a single booking",
     *     description="Returns booking detail",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking found",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="encrypted string")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Booking not found")
     * )
     */
    public function show($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $response = [
            'message' => 'success',
            'data' => $booking
        ];

        $encrypted = EncryptionHelper::encrypt(json_encode($response));

        return response()->json(['data' => $encrypted]);
    }

    /**
     * @OA\Put(
     *     path="/api/bookings/{id}",
     *     operationId="updateBooking",
     *     tags={"Bookings"},
     *     summary="Update booking",
     *     description="Updates existing booking data",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="booking_date", type="string", format="date", example="2025-07-25"),
     *             @OA\Property(property="start_time", type="string", example="14:00"),
     *             @OA\Property(property="end_time", type="string", example="16:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking updated",
     *         @OA\JsonContent(@OA\Property(property="data", type="string", example="encrypted string"))
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->update($request->all());

        $response = [
            'message' => 'Booking updated successfully',
            'data' => $booking
        ];

        $encrypted = EncryptionHelper::encrypt(json_encode($response));

        return response()->json(['data' => $encrypted]);
    }

    /**
     * @OA\Delete(
     *     path="/api/bookings/{id}",
     *     operationId="deleteBooking",
     *     tags={"Bookings"},
     *     summary="Delete booking",
     *     description="Deletes booking by ID",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking deleted",
     *         @OA\JsonContent(@OA\Property(property="data", type="string", example="encrypted string"))
     *     )
     * )
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->delete();

        $response = [
            'message' => 'Booking deleted successfully',
            'data' => ['id' => $id]
        ];

        $encrypted = EncryptionHelper::encrypt(json_encode($response));

        return response()->json(['data' => $encrypted]);
    }

    /**
     * @OA\Post(
     *     path="/api/bookings/decrypt",
     *     operationId="decryptBookingData",
     *     tags={"Bookings"},
     *     summary="Decrypt encrypted booking data",
     *     description="Returns original data from encrypted booking response",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="encrypted-data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Decryption successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function decryptResponse(Request $request)
    {
        try {
            $decrypted = EncryptionHelper::decrypt($request->input('data'));
            $decoded = json_decode($decrypted, true);

            return response()->json($decoded);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Decrypt failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}