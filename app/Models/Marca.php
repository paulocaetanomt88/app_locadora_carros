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
        /* Parâmetros de validação do unique:marcas
         * 1º é a tabela
         * 2º é o campo que vai ser pesquisado
         * 3º é o id que deve ser ignorado, mas se este for omitido (como no método store não informa o id) não será permitido inclusão de dados em duplicidade
         * Sendo assim, a diferenciação que o laravel faz entre uma chamada do método store() para o update() é definida graças à sobrecarga do método unique
        */
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
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
