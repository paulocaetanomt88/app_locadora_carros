<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    private $cliente;
    
    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente);

        if ($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $clienteRepository->selectAtributos($request->atributos);
        }

        // return response()->json($this->marca->get(), 200);
        return response()->json($clienteRepository->getResultado(), 200); 
    }

    public function store(Request $request)
    {
        $request->validate($this->cliente->rules(), $this->cliente->feedback());

        $cliente = $this->cliente->create([
            'nome' => $request->nome
        ]);

        return response()->json($cliente, 201);
    }

    public function show($id)
    {
         $cliente = $this->cliente->find($id);

         if ($cliente === null) {
             return response()->json(['erro' => 'Recurso não encontrado'], 404); 
         }
 
         return response()->json($cliente, 200);
    }

    public function update(Request $request, $id)
    {

        $cliente = $this->cliente->find($id);

        if ($cliente === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe '], 404);
        }

        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            // percorrendo todas as regras definidas no Model
            foreach($cliente->rules() as $input => $regra) {

                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $cliente->feedback());

            // percorrendo os inputs vindos em $request->all()
            foreach($request->all() as $input => $valor) {

                if ($input != null && $input != '_method') { 
                    $cliente->update([
                        $input => $valor,
                    ]);
                }
            }
        }

        if($request->method() === 'PUT') {
            $request->validate($this->cliente->rules(), $cliente->feedback());

            $cliente->update([
                'nome' => $request->nome
            ]);
        }

        return response()->json($cliente, 200);
    }

    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);

        if ($cliente === null) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe '], 404);
        }

        $cliente->delete();

        return response()->json(['msg' => 'O cliente foi removido com sucesso!'], 200);
    }
}
