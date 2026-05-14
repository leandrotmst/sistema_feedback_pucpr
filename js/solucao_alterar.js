document.addEventListener("DOMContentLoaded", () => {
    // Check session
    fetch('../php/get_sessao_analista_dados.php')
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'ok') {
                window.location.href = "../login/login_analista_dados.html";
            }
        });

    const url = new URLSearchParams(window.location.search);
    const id = url.get('id');

    if (id) {
        buscar(id);
    } else {
        alert("Nenhum ID de solução informado.");
        window.history.back();
    }
});

async function buscar(id){
    const retorno = await fetch("../php/solucao_get.php?id="+id);
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        var registro = resposta.data[0];

        document.getElementById('titulo').value = registro.titulo;
        document.getElementById('equipe').value = registro.equipe;
        document.getElementById('descricao').value = registro.descricao;
        document.getElementById("id_solucao").value = registro.id;
    }else{
        alert("Erro, não existe: " + resposta.mensagem);
    }
}

document.getElementById('salvar').addEventListener('click', () => {
    alterar();
});

async function alterar(){
    var titulo = document.getElementById("titulo").value;
    var equipe = document.getElementById("equipe").value;
    var descricao = document.getElementById("descricao").value;
    var id_solucao = document.getElementById("id_solucao").value;

    if (!titulo.trim() || !equipe.trim() || !descricao.trim()) {
        alert("Por favor, preencha todos os campos.");
        return;
    }

    const fd = new FormData();
    fd.append('titulo', titulo);
    fd.append('equipe', equipe);
    fd.append('descricao', descricao);

    try {
        const retorno = await fetch("../php/solucao_alterar.php?id="+id_solucao, {
            method: "POST",
            body: fd
        });
        const resposta = await retorno.json();

        if(resposta.status=='ok'){
            alert("Sucesso: " + resposta.mensagem);
            window.location.href = 'solucao.html';
        }else{
            alert("Erro: " + resposta.mensagem);
        }
    } catch (error) {
        alert("Erro na comunicação com o servidor.");
        console.error(error);
    }
}
