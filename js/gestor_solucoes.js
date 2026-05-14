document.addEventListener('DOMContentLoaded', () => {
    carregarSolucoes();
});

async function carregarSolucoes() {
    try {
        const response = await fetch('../php/gestor_solucoes_get.php');
        const data = await response.json();
        
        if (data.status === 'ok') {
            const tbody = document.getElementById('tabelaSolucoes');
            tbody.innerHTML = '';
            
            data.data.forEach(solucao => {
                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #eee';
                tr.innerHTML = `
                    <td style="padding: 10px;">${solucao.id}</td>
                    <td style="padding: 10px;">${solucao.titulo}</td>
                    <td style="padding: 10px;">${solucao.equipe}</td>
                    <td style="padding: 10px;">${solucao.analista_email}</td>
                    <td style="padding: 10px; display: flex; gap: 5px;">
                        <button class="btn btn-secondary" onclick="alert('Descrição:\\n${solucao.descricao}')">Ver Descrição</button>
                        <button class="btn btn-danger" onclick="apagarSolucao(${solucao.id})">Apagar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar solucoes:', error);
    }
}

async function apagarSolucao(id) {
    if (!confirm('Tem certeza que deseja apagar esta solução?')) {
        return;
    }

    try {
        const response = await fetch('../php/gestor_solucoes_apagar.php?id=' + id);
        const data = await response.json();

        if (data.status === 'ok') {
            alert(data.mensagem);
            carregarSolucoes();
        } else {
            alert(data.mensagem || 'Erro ao apagar solução');
        }
    } catch (error) {
        console.error('Erro ao apagar:', error);
        alert('Erro de comunicação com o servidor');
    }
}
