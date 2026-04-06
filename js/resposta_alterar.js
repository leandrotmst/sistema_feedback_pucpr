document.addEventListener("DOMContentLoaded", () => {
    const url = new URLSearchParams(window.location.search);
    const id = url.get('id');

    buscar(id);
});

async function buscar(id){
    const retorno = await 
    fetch("../php/resposta_get.php?id="+id);
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        var registro = resposta.data[0];

        document.getElementById('emocional').value      = registro.emocional;
        document.getElementById('texto').value      = registro.texto;
        document.getElementById('email_do_funcionario').value      = registro.email_do_funcionario;
        document.getElementById("id_resposta").value = registro.id;
    }else{
        alert("Erro, não existe: " + resposta.mensagem);
    }
}

document.getElementById('salvar').addEventListener('click', () => {
    alterar();
});

async function alterar(){
    var emocional      = document.getElementById("emocional").value;
    var texto      = document.getElementById("texto").value;
    var email_do_funcionario      = document.getElementById("email_do_funcionario").value;
    var id_resposta = document.getElementById("id_resposta").value;

    const fd = new FormData();
    fd.append('emocional', emocional);
    fd.append('texto', texto);
    fd.append('email_do_funcionario', email_do_funcionario);

    const retorno = await 
    fetch("../php/resposta_alterar.php?id="+id_resposta,
    {
        method: "POST",
        body: fd
    });
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        alert("Sucesso: " + resposta.mensagem);
        window.location.href = '../formulario/respostas.html';
    }else{
        alert("Erro: " + resposta.mensagem);
    }
}