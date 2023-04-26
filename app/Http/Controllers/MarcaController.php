<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    private $marca;
    // Criando o método construtor que é executado automaticamente quando um objeto de Marca é instanciado
    // indicando o nome da classe (Marca) na frente do parâmetro $marca, o próprio laravel faz a instância desse objeto do tipo Marca
    public function __construct(Marca $marca) {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$marcas = Marca::all(); // apenas executando o método estático all()
        $marcas = $this->marca->all(); // trabalhando, de fato, com o objeto
        return $marcas;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $marca = Marca::create($request->all());
        $marca = $this->marca->create($request->all());

        return $marca;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->find($id);

        // Controle de fluxo para não mostrar apenas um retorno vazio ou null caso não for encontrado o $id da marca no banco de dados
        if ($marca === null) {
            // retornando um array associativo, o Laravel converte automaticamente isso para JSON
            return['erro' => 'Recurso não encontrado']; // -> isso retorna: { "erro": "Recurso não encontrado" }
        }

        return $marca;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //$marca->update($request->all());
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return ['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe '];
        }

        $marca->update($request->all());

        return $marca;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //$marca->delete();
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return ['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe.'];
        }

        $marca->delete();

        return ['msg' => 'A marca foi removida com sucesso!'];
    }
}
