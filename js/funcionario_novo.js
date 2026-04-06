document.getElementById('enviar').addEventListener('click', () => {
    novo();
});

async function novo(){
    var equipe      = document.getElementById("equipe").value;
    var email     = document.getElementById("email").value;
    var senha     = document.getElementById("senha").value;
    var confsenha = document.getElementById("confsenha").value;

    if(senha===confsenha){
        const fd = new FormData();
        fd.append('equipe', equipe);
        fd.append('email', email);
        fd.append('senha', senha);

        const retorno = await fetch("../php/funcionario_novo.php",
        {
            method: "POST",
            body: fd
        });
        const resposta = await retorno.json();

        if(resposta.status=='ok'){
            alert("Sucesso: " + resposta.mensagem);
            window.location.href = 'funcionario.html';
        }else{
            alert("Erro: " + resposta.mensagem);
        }
    }
    else{
        alert('As senhas devem ser as mesmas');
    }
}