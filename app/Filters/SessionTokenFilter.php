<?php

namespace App\Filters;

use App\Models\Conta_model;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SessionTokenFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $usuarioID = $session->get('user_id');
        $tokenSessao = $session->get('session_token');

        if (!$usuarioID || !$tokenSessao) {
            $session->destroy();
            return redirect()->to('/');
        }

        $usuarioModel = new Conta_model();
        $usuario = $usuarioModel->getUserByID($usuarioID);

        if (!$usuario || $usuario['session_token'] !== $tokenSessao) {
            $session->destroy();
            return redirect()->to('/?token_invalido=1');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
