<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    // trazendo os métodos de validação para a model, podemos tê-los nas controllers que forem criadas a instância desta model em seu construtor
    // * dessa forma, os métodos são criados uma só vez e não precisam ser criados em outros controllers
    public function rules() {
        return [
            'nome' => 'required|unique:marcas|min:3',
            'imagem' => 'required'
        ];
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres'
        ];
    }
}
