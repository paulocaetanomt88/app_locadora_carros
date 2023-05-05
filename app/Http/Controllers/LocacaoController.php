<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    private $locacao;

    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }

    public function index(Request $request)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);

        if ($request->has('atributos_cliente')) {
            $atributos_cliente = 'cliente:id,' . $request->atributos_cliente;

            $locacaoRepository->selectAtributosRegistrosRelacionados($atributos_cliente);
        } else {
            $locacaoRepository->selectAtributosRegistrosRelacionados('cliente');
        }

        if ($request->has('atributos_carro')) {
            $atributos_carro = 'carro:id,' . $request->atributos_carro;

            $locacaoRepository->selectAtributosRegistrosRelacionados($atributos_carro);
        } else {
            $locacaoRepository->selectAtributosRegistrosRelacionados('carro');
        }

        if ($request->has('filtro')) {
            $locacaoRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $locacaoRepository->selectAtributos($request->atributos);
        }

        // return response()->json($this->marca->get(), 200);
        return response()->json($locacaoRepository->getResultado(), 200); 
    }

    public function store(Request $request)
    {
        $request->validate($this->locacao->rules(), $this->locacao->feedback());

        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'periodo_data_inicio' => $request->periodo_data_inicio,
            'periodo_data_final_previsto' => $request->periodo_data_final_previsto,
            'periodo_data_final_realizado' => $request->periodo_data_final_realizado,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final
        ]);

        return response()->json($locacao, 201);
    }

    public function show($id)
    {
        $locacao = $this->locacao->with('cliente')->with('carro')->find($id);

        // Controle de fluxo para não mostrar apenas um retorno vazio ou null caso não for encontrado o $id da marca no banco de dados
        if ($locacao === null) {

            return response()->json(['erro' => 'Recurso não encontrado'], 404); // -> isso retorna: { "erro": "Recurso não encontrado" }
        }

        return response()->json($locacao, 200);
    }

    public function update(Request $request, $id)
    {
        $locacao = $this->locacao->find($id);

        if ($locacao === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe '], 404);
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            // percorrendo todas as regras definidas no Model
            foreach ($locacao->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $locacao->feedback());

            // percorrendo os inputs vindos em $request->all()
            foreach ($request->all() as $input => $valor) {

                if ($input != null && $input != '_method') {
                    $locacao->update([
                        $input => $valor,
                    ]);
                }
            }
        }

        if ($request->method() === 'PUT') {
            $request->validate($this->locacao->rules(), $locacao->feedback());

            $locacao->update([
                'cliente_id' => $request->cliente_id,
                'carro_id' => $request->carro_id,
                'periodo_data_inicio' => $request->periodo_data_inicio,
                'periodo_data_final_previsto' => $request->periodo_data_final_previsto,
                'periodo_data_final_realizado' => $request->periodo_data_final_realizado,
                'valor_diaria' => $request->valor_diaria,
                'km_inicial' => $request->km_inicial,
                'km_final' => $request->km_final
            ]);
        }

        return response()->json($locacao, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $locacao = $this->locacao->find($id);

        if ($locacao == null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso não existe.'], 404);
        }

        $locacao->delete();

        return response()->json(['msg' => 'A locação foi removida com sucesso!'], 200);
    }
}
