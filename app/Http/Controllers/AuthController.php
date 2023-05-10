<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {
        // autenticação (email e senha)
        // obtendo apenas email e senha de request->all()
        $credenciais = $request->all(['email', 'password']);

        // $token recebe o retorno que é gerado pela tentativa de autenticação pela api com base nas credenciais passadas
        $token = auth('api')->attempt($credenciais);

        if ($token) { // usuário autenticado com sucesso
            return response()->json(['token' => $token]);
        } else { // erro de usuário ou senha
            return response()->json(['erro' => 'Usuário ou senha inválido'], 403); // 403 -> forbidden -> proibido
        }

        // retornar um Json Web Token
        return 'login';
    }

    public function logout() {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout foi realizdo com sucesso.']);
    }

    public function refresh() {
        $token = auth('api')->refresh();

        return response()->json(['token' => $token]);
    }

    public function me() {
       return response()->json(auth()->user());
    }
}
