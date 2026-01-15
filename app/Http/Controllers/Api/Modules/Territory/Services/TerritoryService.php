<?php

namespace App\Http\Controllers\Api\Modules\Territory\Services;

use App\Http\Controllers\Api\Modules\Territory\Models\UserPath;
use App\Http\Controllers\Api\Modules\Territory\Models\UserPolygon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;

class TerritoryService extends Controller
{
    /**
     * @throws \Throwable
     */
    public function handlePing(Request $request)
    {
        $user = $request->user();
        $lat = $request->lat;
        $lng = $request->lng;

        // Точність 10м (4 знаки після коми)
        $factor = 10000;
        $gridX = (int)round($lng * $factor);
        $gridY = (int)round($lat * $factor);

        return DB::transaction(function () use ($user, $lat, $lng, $gridX, $gridY) {

            // Перевіряємо, чи ми перетнули свій же "хвіст"
            $overlap = UserPath::where('user_id', $user->id)
                ->where('x', $gridX)
                ->where('y', $gridY)
                ->exists();

            if ($overlap) {
                return $this->captureTerritory($user, $lng, $lat);
            }

            // Додаємо нову ланку в ланцюжок шляху
            UserPath::create([
                'user_id' => $user->id,
                'location' => new Point($lng, $lat, 4326),
                'x' => $gridX,
                'y' => $gridY,
                'color' => $user->color
            ]);

            return response()->json(['type' => 'path_updated']);
        });
    }

    protected function captureTerritory($user, $currentLng, $currentLat)
    {
        $pathPoints = UserPath::where('user_id', $user->id)->orderBy('id')->get();

        if ($pathPoints->count() < 3) return response()->json(['type' => 'too_small']);

        $points = $pathPoints->map(fn($p) => new Point($p->location->longitude, $p->location->latitude))->toArray();

        // Замикаємо лінію
        $points[] = new Point($currentLng, $currentLat); // Точка перетину
        $points[] = $points[0]; // Повернення в старт для Polygon

        $polygon = new Polygon([new LineString($points)]);

        $newArea = UserPolygon::create([
            'user_id' => $user->id,
            'area' => $polygon,
            'color' => $user->color ?? '#FF0000',
        ]);

        // Очищуємо пройдений шлях
        UserPath::where('user_id', $user->id)->delete();

        return response()->json([
            'type' => 'territory_captured',
            'polygon_id' => $newArea->id
        ]);
    }
}
