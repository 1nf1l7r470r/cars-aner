<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscription
 * @package App
 *
 * @property int $id
 * @property int $user_id
 * @property string $number
 */
class Subscription extends Model
{
    /**
     * @param array $vehicleNumbers
     * @param array $bodyNumbers
     * @param array $engineNumbers
     *
     * @return Collection|Subscription[]
     */
    public static function findByNumbers(array $vehicleNumbers, array $bodyNumbers, array $engineNumbers): Collection
    {
        return self::whereIn('number', $vehicleNumbers)
        ->orWhereIn('number', $bodyNumbers)
        ->orWhereIn('number', $engineNumbers)
        ->get();
    }
}
