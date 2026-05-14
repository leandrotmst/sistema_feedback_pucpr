// Assim que a página carregar
window.onload = function() {
    // Verifica sessão do analista de dados
    fetch('../php/get_sessao_analista_dados.php')
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'ok') {
                window.location.href = "../login/login_analista_dados.html";
            }
        });
};

document.getElementById('enviar').addEventListener('click', () => {
    salvarSolucao();
});

async function salvarSolucao(){
    var titulo = document.getElementById("titulo").value;
    var equipe = document.getElementById("equipe").value;
    var descricao = document.getElementById("descricao").value;

    // Validações
    if (!titulo.trim() || !equipe.trim() || !descricao.trim()) {
        alert("Por favor, preencha todos os campos.");
        return;
    }

    const fd = new FormData();
    fd.append('titulo', titulo);
    fd.append('equipe', equipe);
    fd.append('descricao', descricao);

    try {
        const retorno = await fetch("../php/solucao_nova.php", {
            method: "POST",
            body: fd
        });
        const resposta = await retorno.json();

        if(resposta.status == 'ok'){
            alert("Sucesso: " + resposta.mensagem);
            window.location.href = '../analista_dados/solucao.html';
        } else {
            alert("Erro: " + resposta.mensagem);
        }
    } catch (error) {
        alert("Erro na comunicação com o servidor.");
        console.error(error);
    }
}
