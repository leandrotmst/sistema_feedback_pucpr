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
    const senha_atual = document.getElementById('senha_atual').value;
    const senha_nova = document.getElementById('senha_nova').value;
    const senha_nova_confirmacao = document.getElementById('senha_nova_confirmacao').value;

    if (!email) {
        alert("Preencha o e-mail");
        return;
    }

    if (!email.includes('@')) {
        alert("Por favor, informe um e-mail válido contendo '@'.");
        return;
    }

    if (senha_nova || senha_atual || senha_nova_confirmacao) {
        if (!senha_atual) {
            alert("Para alterar a senha, você deve informar a senha atual.");
            return;
        }
        if (!senha_nova) {
            alert("Para alterar a senha, você deve informar a nova senha.");
            return;
        }
        if (senha_nova !== senha_nova_confirmacao) {
            alert("A nova senha e a confirmação não coincidem.");
            return;
        }
    }

    const fd = new FormData();
    fd.append('email', email);
    fd.append('senha_atual', senha_atual);
    fd.append('senha_nova', senha_nova);

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
