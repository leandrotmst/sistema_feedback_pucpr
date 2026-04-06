// Assim que a página carregar
window.onload = function() {
    fetch('../php/get_sessao_gestor.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                // Coloca o ID automaticamente no input hidden
                document.getElementById('id_gestor').value = data.gestor_id;
            } else {
                // Se não tiver sessão, manda de volta pro login
                window.location.href = "../login/login_gestor.html";
            }
        });
};

document.getElementById('enviar').addEventListener('click', () => {
    novo();
});

async function novo(){
    var equipe      = document.getElementById("equipe").value;
    var email     = document.getElementById("email").value;
    var senha     = document.getElementById("senha").value;
    var id_gestor     = document.getElementById("id_gestor").value;

    const fd = new FormData();
    fd.append('equipe', equipe);
    fd.append('email', email);
    fd.append('senha', senha);
    fd.append('id_gestor', id_gestor);

    const retorno = await fetch("../php/funcionario_novo.php",
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