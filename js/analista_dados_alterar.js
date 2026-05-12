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
    var senha_atual = document.getElementById("senha_atual").value;
    var senha_nova  = document.getElementById("senha_nova").value;
    var senha_nova_confirmacao = document.getElementById("senha_nova_confirmacao").value;
    var id_analista_dados = document.getElementById("id_analista_dados").value;

    // Validação de email
    if (!email.includes('@')) {
        alert("Por favor, informe um e-mail válido contendo '@'.");
        return;
    }

    // Validação de senhas
    if (senha_nova || senha_atual || senha_nova_confirmacao) {
        if (!senha_atual) {
            alert("Para alterar a senha, você deve informar a senha atual.");
            return;
        }
        if (!senha_nova) {
            alert("Para alterar a senha, você deve informar a nova senha.");
            return;
        }
        if (senha_nova !== senha_nova_confirmacao) {
            alert("A nova senha e a confirmação não coincidem.");
            return;
        }
    }

    const fd = new FormData();
    fd.append('email', email);
    fd.append('senha_atual', senha_atual);
    fd.append('senha_nova', senha_nova);

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