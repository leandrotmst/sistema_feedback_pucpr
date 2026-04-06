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

        const radios = document.getElementsByName('nivel');
        for (let i = 0; i < radios.length; i++) {
            if (radios[i].value == registro.emocional) {
                radios[i].checked = true;
                break;
            }
        }
        document.getElementById('texto').value      = registro.texto;
        document.getElementById("id_resposta").value = registro.id;
    }else{
        alert("Erro, não existe: " + resposta.mensagem);
    }
}

document.getElementById('salvar').addEventListener('click', () => {
    alterar();
});

async function alterar(){
    const nivelRadio = document.querySelector('input[name="nivel"]:checked');
    var texto = document.getElementById("texto").value;
    var id_resposta = document.getElementById("id_resposta").value;

    if (!nivelRadio) {
        alert("Por favor, selecione um nível emocional.");
        return;
    }

    const fd = new FormData();
    fd.append('emocional', nivelRadio.value); // Envia o valor do rádio selecionado
    fd.append('texto', texto);

    const retorno = await fetch("../php/resposta_alterar.php?id=" + id_resposta, {
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