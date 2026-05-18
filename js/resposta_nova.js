var emailLogado = "";
var equipeLogado = "";

// 1. Ao carregar a página, identifica quem é o funcionário logado
window.onload = function() {
    const day = new Date().getDay();
    if (day >= 1 && day <= 3) {
        alert("Fora do prazo! O formulário só pode ser preenchido de quinta-feira a domingo.");
        window.location.href = "respostas.html";
        return;
    }
    fetch('../php/get_sessao_funcionario.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                emailLogado = data.email;
                equipeLogado = data.equipe;
                document.getElementById('equipe').value = equipeLogado;
            } else {
                alert("Sessão expirada ou inválida. Faça login novamente.");
                window.location.href = "../login/login_funcionario.html";
            }
        });
};

document.getElementById('enviar').addEventListener('click', () => {
    nova();
});

async function nova() {
    const nivelRadio = document.querySelector('input[name="nivel"]:checked');
    const texto = document.getElementById("texto").value;

    if (!nivelRadio || !texto.trim()) {
        alert("Preencha todos os campos obrigatórios (humor e resumo).");
        return;
    }

    // Criando o corpo da requisição
    const fd = new FormData();
    fd.append("nivel", nivelRadio.value);
    fd.append("texto", texto);
    fd.append("equipe", equipeLogado);

    const retorno = await fetch("../php/resposta_nova.php", {
        method: "POST",
        body: fd,
    });
    
    const resposta = await retorno.json();

    if (resposta.status === "ok") {
        alert("Resposta enviada com sucesso!");
        window.location.href = "respostas.html";
    } else {
        alert("ERRO: " + resposta.mensagem);
    }
}