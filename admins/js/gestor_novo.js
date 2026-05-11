document.addEventListener('DOMContentLoaded', () => {
    admin_valida_sessao();
});

document.getElementById('enviar').addEventListener('click', async () => {
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
        const retorno = await fetch('../php/gestores/gestor_novo.php', {
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
