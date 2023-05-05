<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasFactory;

    protected $table ='locacoes';

    protected $fillable = [
        'cliente_id',
        'carro_id',
        'periodo_data_inicio',
        'periodo_data_final_previsto',
        'periodo_data_final_realizado',
        'valor_diaria',
        'km_inicial',
        'km_final'
    ];

    public function rules() {
        return [
            'cliente_id' => 'exists:clientes,id',
            'carro_id' => 'exists:carros,id',
            'periodo_data_inicio' => 'required',
            'periodo_data_final_previsto' => 'required',
            'periodo_data_final_realizado' => 'required',
            'valor_diaria' => 'required',
            'km_inicial' => 'required',
            'km_final' => 'required',
        ];
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'exists' => 'O registro não existe na tabela referenciada'
        ];
    }

    public function carro() {
        return $this->belongsTo('App\Models\Carro');
    }

    public function cliente() {
        return $this->belongsTo('App\Models\Cliente');
    }
}
