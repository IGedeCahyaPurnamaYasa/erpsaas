<?php

namespace Database\Factories\Common;

use App\Models\Common\StockKeepingUnit;
use Database\Factories\Concerns\HasParentRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockKeepingUnit>
 */
class StockKeepingUnitFactory extends Factory
{
    use HasParentRelationship;

    /**
     * The name of the factory's corresponding model.
     */
    protected $model = StockKeepingUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'code' => $this->faker->words(3, true),
            'description' => null,
            'increment' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
