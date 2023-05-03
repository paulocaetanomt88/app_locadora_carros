<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules() {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean' // true ou false, 1 ou 0, "1" ou "0"
        ];
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG, JPEG ou JPG',
            'nome.unique' => 'O nome da modelo já existe',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres',
            'numero_portas.digits_between' => 'Deve ter no mínimo 1 e no máximo 5 portas',
            'lugares.digits_between' => 'Deve ter no mínimo 1 e no máximo 20 lugares',
            'boolean' => 'O campo :attribute deve ser do tipo boleano'
        ];
    }
}
