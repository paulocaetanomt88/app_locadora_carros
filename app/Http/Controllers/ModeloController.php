<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{
    private $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        // aplica o método with() para pegar apenas os atributos selecionados se a requisição conter 'atributos_marca', senão traz todos os dados da marca deste modelo
        if ($request->has('atributos_marca')) {
            $atributos_marca = 'marca:id,' . $request->atributos_marca;

            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        // aplica o método where() passando as condições para retorno (ou não) dos registros se a requisição conter 'filtro'
        if ($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        // aplica o método select() para selecionar apenas os atributos especificados se a requisição conter 'atributos'
        if ($request->has('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        }

        // return response()->json($this->marca->get(), 200);
        return response()->json($modeloRepository->getResultado(), 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules(), $this->modelo->feedback());

        $image = $request->file('imagem');

        // quando o método store de request do tipo file é executado, seu retorno é o caminho (diretório + / + nome do arquivo gerado + .png)
        $imagem_urn = $image->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);

        // Controle de fluxo para não mostrar apenas um retorno vazio ou null caso não for encontrado o $id da marca no banco de dados
        if ($modelo === null) {

            return response()->json(['erro' => 'Recurso não encontrado'], 404); // -> isso retorna: { "erro": "Recurso não encontrado" }
        }

        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe '], 404);
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            // percorrendo todas as regras definidas no Model
            foreach ($modelo->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $modelo->feedback());

            // percorrendo os inputs vindos em $request->all()
            foreach ($request->all() as $input => $valor) {

                if ($input != null && $input != '_method') {
                    if ($request->hasFile($input)) {
                        $imagem = $request->file($input);

                        if ($imagem != null) {
                            // remove a imagem antiga
                            Storage::disk('public')->delete($modelo->imagem);

                            // armazena a imagem nova
                            $imagem_urn = $imagem->store('imagens/modelos', 'public');

                            // atualiza o path da imagem na tabela modelos no banco de dados
                            $modelo->update([
                                'imagem' => $imagem_urn
                            ]);
                        }
                    } else {
                        $modelo->update([
                            $input => $valor,
                        ]);
                    }
                }
            }
        }

        if ($request->method() === 'PUT') {
            $request->validate($this->modelo->rules(), $modelo->feedback());

            if ($request->file('imagem')) {
                Storage::disk('public')->delete($modelo->imagem);
            }

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/modelos', 'public');

            $modelo->update([
                'marca_id' => $request->marca_id,
                'nome' => $request->nome,
                'imagem' => $imagem_urn,
                'numero_portas' => $request->numero_portas,
                'lugares' => $request->lugares,
                'air_bag' => $request->air_bag,
                'abs' => $request->abs
            ]);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo == null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso não existe.'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();

        return response()->json(['msg' => 'O modelo foi removido com sucesso!'], 200);
    }
}
