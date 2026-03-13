<?php

namespace Database\Factories;

use App\Models\UnitOfMeasurement;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitOfMeasurementFactory extends Factory
{
    protected $model = UnitOfMeasurement::class;

    public function definition(): array
    {
        $units = [
            ['unit' => 'piece', 'abbreviation' => 'pc'],
            ['unit' => 'kilogram', 'abbreviation' => 'kg'],
            ['unit' => 'gram', 'abbreviation' => 'g'],
            ['unit' => 'milligram', 'abbreviation' => 'mg'],
            ['unit' => 'ton', 'abbreviation' => 't'],
            ['unit' => 'liter', 'abbreviation' => 'L'],
            ['unit' => 'milliliter', 'abbreviation' => 'mL'],
            ['unit' => 'gallon', 'abbreviation' => 'gal'],
            ['unit' => 'quart', 'abbreviation' => 'qt'],
            ['unit' => 'pint', 'abbreviation' => 'pt'],
            ['unit' => 'meter', 'abbreviation' => 'm'],
            ['unit' => 'centimeter', 'abbreviation' => 'cm'],
            ['unit' => 'millimeter', 'abbreviation' => 'mm'],
            ['unit' => 'inch', 'abbreviation' => 'in'],
            ['unit' => 'foot', 'abbreviation' => 'ft'],
            ['unit' => 'yard', 'abbreviation' => 'yd'],
            ['unit' => 'pack', 'abbreviation' => 'pk'],
            ['unit' => 'box', 'abbreviation' => 'bx'],
            ['unit' => 'dozen', 'abbreviation' => 'dz'],
            ['unit' => 'tray', 'abbreviation' => 'try'],
            ['unit' => 'set', 'abbreviation' => 'set'],
            ['unit' => 'bundle', 'abbreviation' => 'bdl'],
            ['unit' => 'roll', 'abbreviation' => 'roll'],
            ['unit' => 'bag', 'abbreviation' => 'bag'],
            ['unit' => 'sack', 'abbreviation' => 'sk'],
            ['unit' => 'carton', 'abbreviation' => 'ctn'],
            ['unit' => 'barrel', 'abbreviation' => 'bbl'],
            ['unit' => 'bottle', 'abbreviation' => 'btl'],
            ['unit' => 'can', 'abbreviation' => 'can'],
            ['unit' => 'sheet', 'abbreviation' => 'sht'],
            ['unit' => 'tablet', 'abbreviation' => 'tab'],
            ['unit' => 'capsule', 'abbreviation' => 'cap'],
            ['unit' => 'drop', 'abbreviation' => 'd'],
            ['unit' => 'spray', 'abbreviation' => 'spr'],
            ['unit' => 'unit', 'abbreviation' => 'u'],
            ['unit' => 'tube', 'abbreviation' => 'tbe'],
            ['unit' => 'pouch', 'abbreviation' => 'pch'],
            ['unit' => 'ream', 'abbreviation' => 'rm'],
        ];

        $unit = $this->faker->randomElement($units);

        return [
            'terminology' => ucfirst($unit['unit']),
            'abbreviation' => strtoupper($unit['abbreviation']),
            'active_status' => 1,
            'deleted_status' => 0,
        ];
    }
}

