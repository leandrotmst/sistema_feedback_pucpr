async function logout(){
    const retorno = await fetch('../php/funcionario_logout.php');
    const resposta = await retorno.json();

    if(resposta.status == 'ok'){
        window.location.href = '../index.html';
    }
}

async function carregarPontuacao(){
    const retorno = await fetch('../php/get_sessao_funcionario.php');
    const resposta = await retorno.json();

    if(resposta.status === 'ok'){
        document.getElementById('score-value').innerText = resposta.pontuacao ?? 0;
    }
}

carregarPontuacao();