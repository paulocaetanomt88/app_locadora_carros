<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;

    protected $fillable = ['modelo_id', 'placa', 'disponivel', 'km'];

    public function rules() {
        return [
            'modelo_id' => 'exists:modelos,id',
            'placa' => 'required',
            'disponivel' => 'required',
            'km' => 'required'
        ];
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'exists' => 'O registro não existe na tabela referenciada'
        ];
    }

    public function modelo() {
        return $this->belongsTo('App\Models\Modelo');
    }
}
