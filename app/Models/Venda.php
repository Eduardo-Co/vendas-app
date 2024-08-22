<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantidade_total',
        'valor_total',
        'user_id'
    ];

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'venda_produto')
                    ->withPivot('quantidade', 'valor_unitario');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
