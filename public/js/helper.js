function obterDataHoraFormatada() {
    let agora = new Date();
    let dia = String(agora.getDate()).padStart(2, '0');
    let mes = String(agora.getMonth() + 1).padStart(2, '0');
    let ano = agora.getFullYear();
    let horas = String(agora.getHours()).padStart(2, '0');
    let minutos = String(agora.getMinutes()).padStart(2, '0');
    let segundos = String(agora.getSeconds()).padStart(2, '0');

    return `${dia}/${mes}/${ano} ${horas}:${minutos}:${segundos}`;
}

function obterDataHora() {
    let agora = new Date();
    let dia = String(agora.getDate()).padStart(2, '0');
    let mes = String(agora.getMonth() + 1).padStart(2, '0');
    let ano = agora.getFullYear();
    let horas = String(agora.getHours()).padStart(2, '0');
    let minutos = String(agora.getMinutes()).padStart(2, '0');
    let segundos = String(agora.getSeconds()).padStart(2, '0');

    return `${ano}-${mes}-${dia} ${horas}:${minutos}:${segundos}`;
}

