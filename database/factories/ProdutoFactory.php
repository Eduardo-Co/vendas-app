<?php

namespace Database\Factories;

use App\Models\Produto;
use App\Models\Categoria; 
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->word,
            'valor' => $this->faker->randomFloat(2, 1, 1000), 
            'quantidade' => $this->faker->numberBetween(1, 100),
            'categoria_id' => Categoria::inRandomOrder()->first()->id, 
        ];
    }
}
