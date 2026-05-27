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
                window.location.href = "../login/login_funcionario.html";
            }
        });
    setWeekField();
};

document.getElementById('enviar').addEventListener('click', () => {
    nova();
});

function getCurrentWeekValue() {
    const date = new Date();
    date.setHours(0,0,0,0);
    date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
    const week1 = new Date(date.getFullYear(), 0, 4);
    const weekNumber = 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
    return `${date.getFullYear()}-W${weekNumber.toString().padStart(2, '0')}`;
}

function setWeekField() {
    const semanaInput = document.getElementById('semana');
    if (semanaInput) {
        semanaInput.value = getCurrentWeekValue();
    }
}

async function nova() {
    var nivelRadio = document.querySelector('input[name="nivel"]:checked');
    var texto = document.getElementById("texto").value;
    var semana = document.getElementById("semana")?.value || "";
    var instagram = document.getElementById("instagram").value;

    if (!nivelRadio || !texto.trim() || !semana) {
        alert("Preencha todos os campos obrigatórios (humor, semana e resumo).");
        return;
    }

    // Criando o corpo da requisição
    const fd = new FormData();
    fd.append("nivel", nivelRadio.value);
    fd.append("texto", texto);
    fd.append("semana", semana);
    fd.append("equipe", equipeLogado);
    fd.append("instagram", instagram);

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