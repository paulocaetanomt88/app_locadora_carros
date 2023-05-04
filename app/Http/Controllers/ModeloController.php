<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        //dd($request->get('atributos')); // "id,nome,imagem"

        $modelos = array();

        

        if ($request->has('atributos')) {
            $atributos = $request->atributos; // retorna valor semelhante a "id,nome,imagem", ou seja, os atributos estão juntos em uma string só
            
            // select() espera por valores como 'id', 'nome', 'imagem'
            // por isso usamos selectRaw que aceita string nesse formato "id,nome,imagem"
            // para recuperar, também, o relacionamento com Marca, precisamos passar o id da marca (marca_id).
            // Exemplo: http://127.0.0.1:8000/api/modelo?atributos=id,nome,lugares,marca_id
            $modelos = $this->modelo->selectRaw($atributos);
        } else {
            $modelos = $this->modelo;
        }

        if ($request->has('filtro')) {

            $filtros = explode(';', $request->filtro);

            foreach($filtros as $key => $condicao) {
                $c = explode(':', $condicao);
                $modelos = $modelos->where($c[0], $c[1], $c[2]);
            }
        }

        if ($request->has('atributos_marca')) {
            // Filtrando atributos da Marca
            $atributos_marca = $request->atributos_marca;

            // A instrução ->with() permite que seja passado a 'relação: coluna1, coluna2, coluna 3...' para filtrar e trazer apenas campos específicos
            $modelos = $modelos->with('marca:id,'.$atributos_marca)->get();
        } else {
            $modelos = $modelos->with('marca')->get();
        }

        // utilizando o relacionamento (método marca da model Modelo), e precisamos usar o método get() ao invés de all()
        // all() -> criando um objeto de consulta + get() = collection
        // get() -> modificar a consulta -> collection
        return response()->json($modelos, 200);
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
