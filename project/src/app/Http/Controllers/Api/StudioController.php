<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Helper\EncryptionHelper;

/**
 * @OA\Tag(
 *     name="Studios",
 *     description="Manajemen Studio"
 * )
 */
class StudioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/studios",
     *     operationId="getStudios",
     *     tags={"Studios"},
     *     summary="Get all studios",
     *     description="Returns a list of all studios",
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
        $data = Studio::all();

        $response = [
            'message' => 'success',
            'data' => $data
        ];

        return response(
            EncryptionHelper::encrypt(json_encode($response)),
            200,
            ['Content-Type' => 'text/plain']
        );
    }


    /**
     * @OA\Post(
     *     path="/api/studios",
     *     operationId="createStudio",
     *     tags={"Studios"},
     *     summary="Create a new studio",
     *     description="Stores a new studio",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price_per_hour", "capacity"},
     *             @OA\Property(property="name", type="string", example="Studio A"),
     *             @OA\Property(property="description", type="string", example="Studio untuk produk dan portrait"),
     *             @OA\Property(property="price_per_hour", type="number", format="float", example=250000),
     *             @OA\Property(property="capacity", type="integer", example=4)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Studio created",
     *         @OA\JsonContent(@OA\Property(property="data", type="string", example="encrypted string"))
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric',
            'capacity' => 'required|integer'
        ]);

        $studio = Studio::create($validated);

        $response = [
            'message' => 'Studio created successfully',
            'data' => $studio
        ];

        return response()->json([
            'data' => EncryptionHelper::encrypt(json_encode($response))
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/studios/{id}",
     *     operationId="getStudioById",
     *     tags={"Studios"},
     *     summary="Get a single studio",
     *     description="Returns studio detail",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Studio found",
     *         @OA\JsonContent(@OA\Property(property="data", type="string", example="encrypted string"))
     *     ),
     *     @OA\Response(response=404, description="Studio not found")
     * )
     */
    public function show($id)
    {
        $studio = Studio::find($id);

        if (!$studio) {
            return response()->json(['message' => 'Studio not found'], 404);
        }

        $response = [
            'message' => 'success',
            'data' => $studio
        ];

        return response()->json([
            'data' => EncryptionHelper::encrypt(json_encode($response))
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/studios/{id}",
     *     operationId="updateStudio",
     *     tags={"Studios"},
     *     summary="Update studio",
     *     description="Update studio data by ID",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Studio Baru"),
     *             @OA\Property(property="description", type="string", example="Deskripsi baru"),
     *             @OA\Property(property="price_per_hour", type="number", example=300000),
     *             @OA\Property(property="capacity", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Studio updated",
     *         @OA\JsonContent(@OA\Property(property="data", type="string", example="encrypted string"))
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $studio = Studio::find($id);

        if (!$studio) {
            return response()->json(['message' => 'Studio not found'], 404);
        }

        $studio->update($request->all());

        $response = [
            'message' => 'Studio updated successfully',
            'data' => $studio
        ];

        return response()->json([
            'data' => EncryptionHelper::encrypt(json_encode($response))
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/studios/{id}",
     *     operationId="deleteStudio",
     *     tags={"Studios"},
     *     summary="Delete studio",
     *     description="Deletes studio by ID",
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Studio deleted",
     *         @OA\JsonContent(@OA\Property(property="data", type="string", example="encrypted string"))
     *     )
     * )
     */
    public function destroy($id)
    {
        $studio = Studio::find($id);

        if (!$studio) {
            return response()->json(['message' => 'Studio not found'], 404);
        }

        $studio->delete();

        $response = [
            'message' => 'Studio deleted successfully',
            'data' => ['id' => $id]
        ];

        return response()->json([
            'data' => EncryptionHelper::encrypt(json_encode($response))
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/studios/decrypt",
     *     operationId="decryptStudioData",
     *     tags={"Studios"},
     *     summary="Decrypt encrypted studio data",
     *     description="Returns original data from encrypted studio response",
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
