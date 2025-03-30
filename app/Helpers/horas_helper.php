<?php

use CodeIgniter\I18n\Time;

function calcularValorHoras($inicioAtividade, $fimAtividade, $valorHora)
{
    // Cria objetos Time do CI4
    $inicioAtividade = Time::parse($inicioAtividade);
    $fimAtividade = Time::parse($fimAtividade);

    // Calcula diferença em segundos
    $segundosTrabalhados = $fimAtividade->getTimestamp() - $inicioAtividade->getTimestamp();

    // Converte para horas decimais
    $horasTrabalhadas = $segundosTrabalhados / 3600;

    // Calcula o valor da atividade
    $valorAtividade = $horasTrabalhadas * $valorHora;

    return [
        "horasTrabalhadas"  => $horasTrabalhadas = round($horasTrabalhadas, 2),
        "valorFaturado"     => $valorAtividade = number_format($valorAtividade, 2, '.', ',')
    ];
}
