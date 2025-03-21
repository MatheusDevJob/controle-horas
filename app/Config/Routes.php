<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/',                                   'Home::index');
$routes->get('criar_conta',                         'Home::criar_conta');
$routes->post('login',                              'Home::login');
$routes->post('logout',                             'Home::logout');
$routes->post('cadastrar_usuario',                  'Home::cadastrar_usuario');


$routes->group("sistema", function ($rotas) {
    $rotas->get("/",                                'Home::home');
    $rotas->post("iniciar_turno",                   'Atividades::iniciar_turno');
    $rotas->post("concluir_turno",                  'Atividades::concluir_turno');
    $rotas->post("iniciar_atividade",               'Atividades::iniciar_atividade');
    $rotas->post("concluir_atividade",              'Atividades::concluir_atividade');

    $rotas->group("adm", function ($adm) {
        $adm->post("getClientes",                   "Clientes::getClientes");
    });
});
