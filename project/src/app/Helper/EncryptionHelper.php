<?php

namespace App\Helper;

use Illuminate\Support\Facades\Crypt;

class EncryptionHelper
{
    public static function encrypt($data)
    {
        return Crypt::encryptString($data);
    }

    public static function decrypt($data)
    {
        try {
            return Crypt::decryptString($data);
        } catch (\Exception $e) {
            return null;
        }
    }
}

