<?php

namespace App\Http\Controllers\Api\Modules\Territory\Controllers;

use App\Http\Controllers\Api\Modules\Territory\Models\UserPath;
use App\Http\Controllers\Api\Modules\Territory\Services\TerritoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TerritoryController
{
    public function __construct(private readonly TerritoryService $territoryService)
    {
    }

    /**
     * @throws Throwable
     */
    public function ping(Request $request)
    {
        return $this->territoryService->handlePing($request);
    }
}
