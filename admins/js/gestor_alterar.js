document.addEventListener('DOMContentLoaded', () => {
    admin_valida_sessao();
    buscarDadosGestor();
});

function getParametroId() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

async function buscarDadosGestor() {
    const id = getParametroId();
    if (!id) {
        alert("ID do gestor não encontrado");
        window.location.href = 'index.html';
        return;
    }

    try {
        const retorno = await fetch('../php/gestores/gestor_get.php?id=' + id);
        const resposta = await retorno.json();

        if (resposta.status == 'ok' && resposta.data.length > 0) {
            document.getElementById('email').value = resposta.data[0].email;
            document.getElementById('senha').value = resposta.data[0].senha;
        } else {
            alert("Gestor não encontrado");
            window.location.href = 'index.html';
        }
    } catch (error) {
        console.error("Erro ao buscar dados:", error);
    }
}

document.getElementById('enviar').addEventListener('click', async () => {
    const id = getParametroId();
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;

    if (!email || !senha) {
        alert("Preencha todos os campos");
        return;
    }

    const fd = new FormData();
    fd.append('email', email);
    fd.append('senha', senha);

    try {
        const retorno = await fetch('../php/gestores/gestor_alterar.php?id=' + id, {
            method: 'POST',
            body: fd
        });
        const resposta = await retorno.json();

        if (resposta.status == 'ok') {
            alert(resposta.mensagem);
            window.location.href = 'index.html';
        } else {
            alert(resposta.mensagem);
        }
    } catch (error) {
        console.error("Erro:", error);
        alert("Erro ao comunicar com o servidor");
    }
});
