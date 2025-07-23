<?php

use App\Helpers\EncryptionHelper;
use Illuminate\Support\Facades\Http;

public function index()
{
    $response = Http::get(env('PUBLIC_STUDIO_API', 'http://nginx_mppl/api/public-studios'));

    $studios = [];

    if ($response->successful()) {
        $encrypted = $response->json('data');

        // decrypt & parse JSON
        $decrypted = EncryptionHelper::decrypt($encrypted);
        $decoded = json_decode($decrypted, true);

        if (!empty($decoded['data'])) {
            $studios = $decoded['data'];
        }
    }

    return view('frontend.studio-list', compact('studios'));
}
