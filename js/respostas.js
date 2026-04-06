if (document.getElementById("lista")) {
    resposta.buscarLista();
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

