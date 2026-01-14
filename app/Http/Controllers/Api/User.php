<?php

namespace App\Http\Controllers\Api;

class User
{
    public function me()
    {
        return auth()->user();
    }
}
