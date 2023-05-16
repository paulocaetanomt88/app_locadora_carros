<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use Illuminate\Http\Request;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller
{
    // criando a propriedade marca de forma privada
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
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);

        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,' . $request->atributos_modelos;

            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }

        // return response()->json($this->marca->get(), 200);
        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }



    /**
     * O método store espera Request $request que vem via POST
     * e pelo método store, em caso de sucesso, o próprio Laravel retorna o status code 201 Created
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $image = $request->file('imagem');

        // quando o método store de request do tipo file é executado, seu retorno é o caminho (diretório + / + nome do arquivo gerado + .png)
        $imagem_urn = $image->store('imagens/marcas', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // carrega a marca de acordo com o id recebido junto com os modelos que pertencem a esta marca
        $marca = $this->marca->with('modelos')->find($id);

        // Controle de fluxo para não mostrar apenas um retorno vazio ou null caso não for encontrado o $id da marca no banco de dados
        if ($marca === null) {

            return response()->json(['erro' => 'Recurso não encontrado'], 404); // -> isso retorna: { "erro": "Recurso não encontrado" }
        }

        return response()->json($marca, 200);
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
        //injetando o método da model Marca no controller MarcaController
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe '], 404);
        }

        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            // percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $regra) {

                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());



            // percorrendo os inputs vindos em $request->all()
            foreach($request->all() as $input => $valor) {

                if ($input != null && $input != '_method') {
                    if ($request->hasFile($input)) {
                        $imagem = $request->file($input);

                        if ($imagem != null) {
                            // remove a imagem antiga
                            Storage::disk('public')->delete($marca->imagem);

                            // armazena a imagem nova
                            $imagem_urn = $imagem->store('imagens/marcas', 'public');

                            // atualiza o path da imagem na tabela Marcas no banco de dados
                            $marca->update([
                                'imagem' => $imagem_urn
                            ]);
                        }
                    } else {
                        $marca->update([
                            $input => $valor,
                        ]);
                    }
                }
            }
        }

        if($request->method() === 'PUT') {
            $request->validate($this->marca->rules(), $marca->feedback());

            if($request->file('imagem')) {
                Storage::disk('public')->delete($marca->imagem);
            }

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/marcas', 'public');

            $marca->update([
                'nome' => $request->nome,
                'imagem' => $imagem_urn
            ]);
        }



        return response()->json($marca, 200);
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
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe '], 404);
        }


        Storage::disk('public')->delete($marca->imagem);


        $marca->delete();

        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
