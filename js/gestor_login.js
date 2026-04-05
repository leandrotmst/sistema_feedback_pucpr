document.getElementById('enviar').addEventListener('click', () => {
    novo();
});

async function novo(){
    var email     = document.getElementById("email").value;
    var senha     = document.getElementById("senha").value;
    var confsenha = document.getElementById("confsenha").value;

    if(senha===confsenha){
        const fd = new FormData();
        fd.append('email', email);
        fd.append('senha', senha);

        const retorno = await fetch("../php/usuario_novo.php",
        {
            method: "POST",
            body: fd
        });
        const resposta = await retorno.json();

        if(resposta.status=='ok'){
            alert("Sucesso: " + resposta.mensagem);
            window.location.href = 'gestor/index.html';
        }else{
            alert("Erro: " + resposta.mensagem);
        }
    }
    else{
        alert('As senhas devem ser as mesmas');
    }
}