var emailLogado = "";
var equipeLogado = "";

// 1. Ao carregar a página, identifica quem é o funcionário logado
window.onload = function() {
    fetch('../php/get_sessao_funcionario.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                emailLogado = data.email;
                equipeLogado = data.equipe;
                document.getElementById('equipe').value = equipeLogado;
            } else {
                alert("Sessão expirada ou inválida. Faça login novamente.");
                // Se não houver sessão ativa, impede a resposta e volta ao login
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

    // Validação: verifica se o rádio foi marcado
    if (!nivelRadio | !texto.trim()) {
        alert("Preencha todos os campos.");
        return;
    }

    // Criando o corpo da requisição
    const fd = new FormData();
    fd.append("nivel", nivelRadio.value); // Corresponde ao $_POST['nivel'] no PHP
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

// Função auxiliar para exibição (usada na listagem se necessário)
function rotuloNivel(n) {
    const v = Number(n);
    const rotulos = ["Muito baixo", "Baixo", "Moderado baixo", "Moderado", "Alto", "Muito alto"];
    return rotulos[v] || "—";
}