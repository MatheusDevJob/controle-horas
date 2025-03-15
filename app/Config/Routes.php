<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/',                                   'Home::index');
$routes->get('criar_conta',                         'Home::criar_conta');
$routes->post('login',                              'Home::login');
$routes->post('cadastrar_usuario',                  'Home::cadastrar_usuario');


$routes->group("sistema", function ($rotas) {
    $rotas->get("/", 'Home::home');
});
