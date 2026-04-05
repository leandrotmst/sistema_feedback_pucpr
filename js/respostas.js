document.getElementById('enviar').addEventListener('click', () => {
    nova();
});

if (document.getElementById("lista")) {
    resposta.buscarLista();
}


async function nova() {
    const equipe = document.getElementById("equipe").value.trim();
    const nivelRadio = document.querySelector('input[name="nivel"]:checked');

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

    const retorno = await fetch("../php/resposta_nova.php", {
        method: "POST",
        body: fd,
    });
    const resposta = await retorno.json();

    if (resposta.status === "ok") {
        alert("Resposta enviada com sucesso!");
        if (document.getElementById("lista")) {
            buscarLista();
        }
    } else {
        alert("ERRO: " + resposta.mensagem);
    }
}

async function buscarLista() {
    const retorno = await fetch("../php/resposta_get.php");
    const resposta = await retorno.json();

    if (resposta.status === "ok") {
        this.preencherTabela(resposta.data);
    }
}

function preencherTabela(tabela) {
    let html = `
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>Equipe</th>
                <th>Emocional / estresse (0-5)</th>
                <th>Data</th>
            </tr>
    `;
    if (tabela.length === 0) {
        html += `<tr><td colspan="3">Nenhuma resposta ainda.</td></tr>`;
    } else {
        for (let i = 0; i < tabela.length; i++) {
            const r = tabela[i];
            const data = r.criado_em ? r.criado_em : "—";
            html += `
            <tr>
                <td>${r.equipe}</td>
                <td>${r.nivel} — ${this.rotuloNivel(r.nivel)}</td>
                <td>${data}</td>
            </tr>
        `;
        }
    }
    html += "</table>";
    document.getElementById("lista").innerHTML = html;
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
