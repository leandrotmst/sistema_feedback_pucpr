// Assim que a página carregar
window.onload = function() {
    // Verifica sessão, mas não precisa mais do id_gestor
    fetch('../php/get_sessao_gestor.php')
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'ok') {
                // Se não tiver sessão, manda de volta pro login
                window.location.href = "../login/login_gestor.html";
            }
        });
};

document.getElementById('enviar').addEventListener('click', () => {
    novo();
});

async function novo(){
    var email  = document.getElementById("email").value;
    var senha  = document.getElementById("senha").value;

    // Validações
    if (!email.trim() | !senha.trim()) {
        alert("Por favor, preencha todos os campos.");
        return;
    }

    const fd = new FormData();
    fd.append('email', email);
    fd.append('senha', senha);

    const retorno = await fetch("../php/analista_dados_novo.php",
    {
        method: "POST",
        body: fd
    });
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        alert("Sucesso: " + resposta.mensagem);
        window.location.href = '../analista_dados/index.html';
    }else{
        alert("Erro: " + resposta.mensagem);
    }
}