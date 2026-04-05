document.addEventListener("DOMContentLoaded", () => {
    const url = new URLSearchParams(window.location.search);
    const id = url.get('id_usuario');

    buscar(id);
});

async function buscar(id){
    const retorno = await 
    fetch("../php/usuario_get.php?id_usuario="+id);
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        var registro = resposta.data[0];

        document.getElementById('email').value      = registro.email;
        document.getElementById('senha').value      = registro.senha;
        document.getElementById('confsenha').value  = registro.senha;
        document.getElementById("id_usuario").value = registro.id_usuario;
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
    var confsenha  = document.getElementById("confsenha").value;
    var id_usuario = document.getElementById("id_usuario").value;

    if(senha===confsenha){
        const fd = new FormData();
        fd.append('email', email);
        fd.append('senha', senha);

        const retorno = await 
        fetch("../php/usuario_alterar.php?id_usuario="+id_usuario,
        {
            method: "POST",
            body: fd
        });
        const resposta = await retorno.json();

        if(resposta.status=='ok'){
            alert("Sucesso: " + resposta.mensagem);
            window.location.href = 'usuario.html';
        }else{
            alert("Erro: " + resposta.mensagem);
        }
    }
    else{
        alert('As senhas devem ser as mesmas');
    }
}