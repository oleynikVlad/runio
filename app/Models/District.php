<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Polygon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int|null $user_id
 * @property string $area
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|District newModelQuery()
 * @method static Builder<static>|District newQuery()
 * @method static Builder<static>|District query()
 * @method static Builder<static>|District whereArea($value)
 * @method static Builder<static>|District whereCreatedAt($value)
 * @method static Builder<static>|District whereId($value)
 * @method static Builder<static>|District whereName($value)
 * @method static Builder<static>|District whereUpdatedAt($value)
 * @method static Builder<static>|District whereUserId($value)
 * @mixin Eloquent
 */
class District extends Model
{
    protected $casts = [
        'area' => Polygon::class,
    ];
}
