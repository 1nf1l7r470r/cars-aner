<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * Class Vehicle
 * @package App
 *
 * @property int    $id
 * @property string $ovd
 * @property string $brand
 * @property string $color
 * @property string $vehicle_number
 * @property string $body_number
 * @property string $chassis_number
 * @property string $engine_number
 * @property string $theft_data
 * @property string $insert_data
 */
class Vehicle extends Model
{
    use InsertOnDuplicateKey;

    /**
     * @param string $number
     *
     * @return Vehicle|null
     */
    public static function findByNumber(string $number): ?Vehicle
    {
        return self::where('vehicle_number', $number)
            ->orWhere('body_number', $number)
            ->orWhere('engine_number', $number)
            ->first();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = 'ID: ' . $this->id . PHP_EOL;
        $string .= 'Хто додав: ' . $this->ovd . PHP_EOL;
        $string .= 'Бренд: ' . $this->brand . PHP_EOL;

        if ($this->vehicle_number) {
            $string .= 'Державний номер: ' . $this->vehicle_number . PHP_EOL;
        }

        if ($this->body_number) {
            $string .= 'Номер кузову: ' . $this->body_number . PHP_EOL;
        }

        if ($this->chassis_number) {
            $string .= 'Номер шасі: ' . $this->chassis_number . PHP_EOL;
        }

        if ($this->engine_number) {
            $string .= 'Номер двигуну: ' . $this->engine_number . PHP_EOL;
        }

        $string .= 'Дата крадіжки: ' . $this->theft_data . PHP_EOL;
        $string .= 'Дата створення: ' . $this->insert_data . PHP_EOL;

        return $string;
    }
}
