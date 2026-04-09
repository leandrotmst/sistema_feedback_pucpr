async function funcionario_valida_sessao(){
    const retorno = await fetch('../php/funcionario_valida_sessao.php');
    const resposta = await retorno.json();

    if(resposta.status=='nok'){
        window.location.href = '../index.html';
    }
}

async function logout(){
    const retorno = await fetch('../php/gestor_logout.php');
    const resposta = await retorno.json();

    if(resposta.status == 'ok'){
        window.location.href = '../index.html';
    }
}