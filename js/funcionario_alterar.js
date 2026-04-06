document.addEventListener("DOMContentLoaded", () => {
    const url = new URLSearchParams(window.location.search);
    const id = url.get('id');

    buscar(id);
});

async function buscar(id){
    const retorno = await 
    fetch("../php/funcionario_get.php?id="+id);
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        var registro = resposta.data[0];

        document.getElementById('equipe').value      = registro.equipe;
        document.getElementById('email').value      = registro.email;
        document.getElementById('senha').value      = registro.senha;
        document.getElementById("id_funcionario").value = registro.id;
    }else{
        alert("Erro, não existe: " + resposta.mensagem);
    }
}

document.getElementById('salvar').addEventListener('click', () => {
    alterar();
});

async function alterar(){
    var equipe      = document.getElementById("equipe").value;
    var email      = document.getElementById("email").value;
    var senha      = document.getElementById("senha").value;
    var id_funcionario = document.getElementById("id_funcionario").value;

    const fd = new FormData();
    fd.append('equipe', equipe);
    fd.append('email', email);
    fd.append('senha', senha);

    const retorno = await 
    fetch("../php/funcionario_alterar.php?id="+id_funcionario,
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