async function logout(){
    const retorno = await fetch('../php/analista_dados_logout.php');
    const resposta = await retorno.json();

    if(resposta.status == 'ok'){
        window.location.href = '../index.html';
    }
}