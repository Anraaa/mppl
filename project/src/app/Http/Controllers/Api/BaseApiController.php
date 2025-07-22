<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Moon Studio API",
 *     version="1.0",
 *     description="API dokumentasi untuk sistem booking studio foto Moon Studio"
 * )
 * @OA\Server(
 *     url="http://localhost",
 *     description="Moon Studio API Server"
 * )
 *  * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-KEY",
 *     description="API Key untuk mengakses endpoint yang dilindungi"
 * )
 */
class BaseApiController extends BaseController
{
    //
}