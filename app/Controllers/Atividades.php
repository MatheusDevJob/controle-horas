<?php

namespace App\Controllers;

final class Atividades extends BaseController
{
    function iniciar_turno()
    {
        echo json_encode(["status" => true, "msg" => "Turno iniciado as: "]);
    }

    function iniciar_atividade()
    {
        echo json_encode(["status" => true, "msg" => "Atividade iniciado as: "]);
    }

    function concluir_atividade()
    {
        echo json_encode(["status" => true, "msg" => "Atividade concluída as: "]);
    }

    function concluir_turno()
    {
        echo json_encode(["status" => true, "msg" => "Turno concluída as: "]);
    }
}
