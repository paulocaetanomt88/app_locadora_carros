<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

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
    public function index()
    {
        //$marcas = Marca::all(); // apenas executando o método estático all()
        $marcas = $this->marca->all(); // trabalhando, de fato, com o objeto

        return response()->json($marcas, 200);
    }



    /**
     * O método store espera Request $request que vem via POST
     * e pelo método store, em caso de sucesso, o próprio Laravel retorna o status code 201 Created
     */
    public function store(Request $request)
    {
        // regras de validação foram movidas para os métodos rules() e feedback() na model Marca

        // trabalhando com APIs, a validação é diferente e precisa ser feita também no client->(navegador, aplicação, postman) que deve enviar nos Headers uma Key 'Accept' com valor 'application/json'
        // com esse ajuste o client indica ao laravel que sabe lidar com o retorno em json
        // sem esse ajuste, o laravel encaminha para a rota padrão stateless e não vai retornar os feedbacks
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $marca = $this->marca->create($request->all());

        // para evitar confusão, podemos explicitar o status code usando o helper response() passando o status code 201
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
        $marca = $this->marca->find($id);

        // Controle de fluxo para não mostrar apenas um retorno vazio ou null caso não for encontrado o $id da marca no banco de dados
        if ($marca === null) {
            // retornando o helper response() para informar o status code. Sem isso, mesmo não encontrando o recurso, seu status code seria "Status: 200 OK"
            // dessa forma fica mais semântico porque isso indica para o client (navegador ou outro software) que a requisição chegou com sucesso ao webservice REST mas do lado do backend não foi encontrado
            // possibilitando ao frontend exibir uma resposta personalizada mais adequada ao usuário
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

        // aplicando a validação usando os métodos dá model Marca
        $request->validate($marca->rules(), $marca->feedback());

        $marca->update($request->all());

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

        $marca->delete();

        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
