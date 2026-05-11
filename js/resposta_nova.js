var emailLogado = "";
var equipeLogado = "";
var perguntasAtivas = [];

// 1. Ao carregar a página, identifica quem é o funcionário logado
window.onload = function() {
    fetch('../php/get_sessao_funcionario.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                emailLogado = data.email;
                equipeLogado = data.equipe;
                document.getElementById('equipe').value = equipeLogado;
                
                // Busca as perguntas dinâmicas após confirmar a sessão e a equipe
                carregarPerguntasDinamicas();
            } else {
                alert("Sessão expirada ou inválida. Faça login novamente.");
                window.location.href = "../login/login_funcionario.html";
            }
        });
};

function carregarPerguntasDinamicas() {
    fetch('../php/perguntas_get.php')
        .then(res => res.json())
        .then(resposta => {
            if (resposta.status === 'ok' && resposta.data.length > 0) {
                perguntasAtivas = resposta.data;
                renderizarPerguntas(perguntasAtivas);
            }
        })
        .catch(err => console.error("Erro ao carregar perguntas:", err));
}

function renderizarPerguntas(perguntas) {
    const container = document.getElementById('perguntas_dinamicas_container');
    container.innerHTML = ''; // Limpa antes de renderizar

    perguntas.forEach(pergunta => {
        // Criação do HTML da pergunta
        const div = document.createElement('div');
        div.className = 'form-group';
        
        const label = document.createElement('label');
        label.className = 'form-label text-primary font-semibold';
        label.textContent = pergunta.texto_pergunta;
        
        div.appendChild(label);

        if (pergunta.tipo_campo === 'select' && pergunta.opcoes) {
            // Desenha um <select>
            const select = document.createElement('select');
            select.className = 'form-input pergunta-dinamica-input';
            select.dataset.perguntaId = pergunta.id;
            select.dataset.textoPergunta = pergunta.texto_pergunta;
            select.required = true;
            
            // Opção em branco por padrão
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '-- Selecione uma opção --';
            placeholder.disabled = true;
            placeholder.selected = true;
            select.appendChild(placeholder);

            const optionsArr = pergunta.opcoes.split(',');
            optionsArr.forEach(optText => {
                const opt = document.createElement('option');
                opt.value = optText.trim();
                opt.textContent = optText.trim();
                select.appendChild(opt);
            });

            div.appendChild(select);

        } else {
            // Padrão: <input type="text">
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-input pergunta-dinamica-input';
            input.dataset.perguntaId = pergunta.id;
            input.dataset.textoPergunta = pergunta.texto_pergunta;
            input.required = true;
            
            div.appendChild(input);
        }

        container.appendChild(div);
    });
}

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

    // Coletando dados das perguntas dinâmicas
    const inputsDinamicos = document.querySelectorAll('.pergunta-dinamica-input');
    let dadosDinamicos = {};
    let formInvalido = false;

    inputsDinamicos.forEach(input => {
        if (!input.value.trim()) {
            formInvalido = true;
        }
        dadosDinamicos[input.dataset.textoPergunta] = input.value.trim();
    });

    if (formInvalido && inputsDinamicos.length > 0) {
        alert("Por favor, responda a todas as perguntas específicas da sua equipe.");
        return;
    }

    // Transformando o objeto em String JSON
    const jsonDadosDinamicos = JSON.stringify(dadosDinamicos);

    // Criando o corpo da requisição
    const fd = new FormData();
    fd.append("nivel", nivelRadio.value);
    fd.append("texto", texto);
    fd.append("equipe", equipeLogado);
    fd.append("dados_dinamicos", jsonDadosDinamicos); // Enviando o JSON!

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