<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $fillable = [
        'nome',
        'imagem_url',
        'descricao',
    ];
    protected $hidden = [];
    protected $casts = [];

    public function getIconeUrlAttribute()
    {
        return $this->imagem_url ? asset('storage/' . $this->imagem_url) : null;
    }
}
