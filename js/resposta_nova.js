document.getElementById('enviar').addEventListener('click', () => {
    nova();
});

async function nova() {
    const equipe = document.getElementById("equipe").value.trim();
    const nivelRadio = document.querySelector('input[name="nivel"]:checked');
    const texto = document.getElementById("texto").value;

    if (!equipe) {
        alert("Informe o nome da equipe.");
        return;
    }
    if (!nivelRadio) {
        alert("Escolha um nível de 0 a 5 (emocional / estresse na semana).");
        return;
    }

    const fd = new FormData();
    fd.append("equipe", equipe);
    fd.append("nivel", nivelRadio.value);
    fd.append("texto", texto);

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

function rotuloNivel(n) {
    const v = Number(n);
    if (v === 0) {
        return "Muito baixo";
    }
    if (v === 1) {
        return "Baixo";
    }
    if (v === 2) {
        return "Moderado baixo";
    }
    if (v === 3) {
        return "Moderado";
    }
    if (v === 4) {
        return "Alto";
    }
    if (v === 5) {
        return "Muito alto";
    }
    return "—";
}
