<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'nome',
        'imagem_url',
        'valor',
        'quantidade',
        'categoria_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    public function vendas()
    {
        return $this->belongsToMany(Venda::class, 'venda_produto')
                    ->withPivot('quantidade', 'valor_unitario');
    }
}
