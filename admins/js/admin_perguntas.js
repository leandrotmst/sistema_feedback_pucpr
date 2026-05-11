document.addEventListener('DOMContentLoaded', () => {
    carregarPerguntas();

    document.getElementById('formPergunta').addEventListener('submit', (e) => {
        e.preventDefault();
        salvarPergunta();
    });
});

async function carregarPerguntas() {
    try {
        const response = await fetch('../php/perguntas/listar_perguntas.php');
        const data = await response.json();
        
        if (data.status === 'ok') {
            const tbody = document.getElementById('tabelaPerguntas');
            tbody.innerHTML = '';
            
            data.data.forEach(pergunta => {
                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #eee';
                tr.innerHTML = `
                    <td style="padding: 10px;">${pergunta.id}</td>
                    <td style="padding: 10px;">${pergunta.texto_pergunta}</td>
                    <td style="padding: 10px;">${pergunta.equipe_alvo}</td>
                    <td style="padding: 10px;">${pergunta.ativa == 1 ? 'Ativa' : 'Inativa'}</td>
                    <td style="padding: 10px; display: flex; gap: 5px;">
                        <button class="btn btn-secondary" onclick="abrirModalEditar(${pergunta.id}, '${pergunta.texto_pergunta.replace(/'/g, "\\'")}', '${pergunta.equipe_alvo}', '${pergunta.tipo_campo}', ${pergunta.ativa})">Editar</button>
                        <button class="btn btn-danger" onclick="apagarPergunta(${pergunta.id})">Apagar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar perguntas:', error);
    }
}

function abrirModalNova() {
    document.getElementById('modalTitle').textContent = 'Nova Pergunta Global';
    document.getElementById('perguntaId').value = '';
    document.getElementById('perguntaTexto').value = '';
    document.getElementById('perguntaEquipe').value = 'Todas';
    document.getElementById('perguntaTipo').value = 'text';
    document.getElementById('perguntaAtiva').value = '1';
    document.getElementById('modalPergunta').style.display = 'flex';
}

function abrirModalEditar(id, texto, equipe, tipo, ativa) {
    document.getElementById('modalTitle').textContent = 'Editar Pergunta Global';
    document.getElementById('perguntaId').value = id;
    document.getElementById('perguntaTexto').value = texto;
    document.getElementById('perguntaEquipe').value = equipe;
    document.getElementById('perguntaTipo').value = tipo;
    document.getElementById('perguntaAtiva').value = ativa;
    document.getElementById('modalPergunta').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modalPergunta').style.display = 'none';
}

async function salvarPergunta() {
    const id = document.getElementById('perguntaId').value;
    const texto = document.getElementById('perguntaTexto').value;
    const equipe = document.getElementById('perguntaEquipe').value;
    const tipo = document.getElementById('perguntaTipo').value;
    const ativa = document.getElementById('perguntaAtiva').value;

    const formData = new FormData();
    formData.append('texto_pergunta', texto);
    formData.append('equipe_alvo', equipe);
    formData.append('tipo_campo', tipo);
    formData.append('ativa', ativa);
    if (id) formData.append('id', id);

    const url = id ? '../php/perguntas/editar_pergunta.php' : '../php/perguntas/adicionar_pergunta.php';

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.status === 'ok') {
            alert(data.mensagem);
            fecharModal();
            carregarPerguntas();
        } else {
            alert(data.mensagem || 'Erro ao salvar pergunta');
        }
    } catch (error) {
        console.error('Erro ao salvar:', error);
        alert('Erro de comunicação com o servidor');
    }
}

async function apagarPergunta(id) {
    if (!confirm('Tem certeza que deseja apagar esta pergunta?')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);

    try {
        const response = await fetch('../php/perguntas/apagar_pergunta.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.status === 'ok') {
            alert(data.mensagem);
            carregarPerguntas();
        } else {
            alert(data.mensagem || 'Erro ao apagar pergunta');
        }
    } catch (error) {
        console.error('Erro ao apagar:', error);
        alert('Erro de comunicação com o servidor');
    }
}
