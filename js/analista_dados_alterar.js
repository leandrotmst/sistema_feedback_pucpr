document.addEventListener("DOMContentLoaded", () => {
    const url = new URLSearchParams(window.location.search);
    const id = url.get('id');

    buscar(id);
});

async function buscar(id){
    const retorno = await 
    fetch("../php/analista_dados_get.php?id="+id);
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        var registro = resposta.data[0];

        document.getElementById('email').value      = registro.email;
        document.getElementById('senha').value      = registro.senha;
        document.getElementById("id_analista_dados").value = registro.id;
    }else{
        alert("Erro, não existe: " + resposta.mensagem);
    }
}

document.getElementById('salvar').addEventListener('click', () => {
    alterar();
});

async function alterar(){
    var email      = document.getElementById("email").value;
    var senha      = document.getElementById("senha").value;
    var id_analista_dados = document.getElementById("id_analista_dados").value;

    const fd = new FormData();
    fd.append('email', email);
    fd.append('senha', senha);

    const retorno = await 
    fetch("../php/analista_dados_alterar.php?id="+id_analista_dados,
    {
        method: "POST",
        body: fd
    });
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        alert("Sucesso: " + resposta.mensagem);
        window.location.href = '../gestor/index.html';
    }else{
        alert("Erro: " + resposta.mensagem);
    }
}