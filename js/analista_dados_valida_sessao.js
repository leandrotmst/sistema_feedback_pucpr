async function analista_dados_valida_sessao(){
    const retorno = await fetch('../php/analista_dados_valida_sessao.php');
    const resposta = await retorno.json();

    if(resposta.status=='nok'){
        window.location.href = '../index.html';
    }
}

async function logout(){
    const retorno = await fetch('../php/analista_dados_logout.php');
    const resposta = await retorno.json();

    if(resposta.status == 'ok'){
        window.location.href = '../index.html';
    }
}