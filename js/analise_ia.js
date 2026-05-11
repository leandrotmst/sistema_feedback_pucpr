document.addEventListener('DOMContentLoaded', () => {
    // Validação de sessão importada de analista_dados_valida_sessao.js
    analista_dados_valida_sessao();

    const btnGerarAnalise = document.getElementById('btnGerarAnalise');
    
    if (btnGerarAnalise) {
        btnGerarAnalise.addEventListener('click', gerarAnalise);
    }
});

async function gerarAnalise() {
    const loading = document.getElementById('loading');
    const resultadoIA = document.getElementById('resultadoIA');
    const msgErro = document.getElementById('msgErro');
    const btnGerarAnalise = document.getElementById('btnGerarAnalise');

    // Reset UI
    loading.style.display = 'block';
    resultadoIA.style.display = 'none';
    msgErro.style.display = 'none';
    btnGerarAnalise.disabled = true;
    btnGerarAnalise.style.opacity = '0.5';
    btnGerarAnalise.textContent = 'Gerando...';

    try {
        const response = await fetch('../php/gerar_analise_ia.php');
        const resposta = await response.json();

        if (resposta.status === 'ok') {
            // A IA retornou os dados estruturados
            document.getElementById('resumo').textContent = resposta.data.resumo;
            document.getElementById('alertas').textContent = resposta.data.alertas;
            document.getElementById('solucoes').textContent = resposta.data.solucoes_propostas;

            // Mostra os cards
            resultadoIA.style.display = 'flex';
        } else {
            // Pode ser erro do PHP, ausência de dados na semana, etc.
            msgErro.textContent = 'Erro: ' + resposta.mensagem;
            msgErro.style.display = 'block';
        }
    } catch (error) {
        msgErro.textContent = 'Erro de conexão com o servidor ou problema na API da IA.';
        msgErro.style.display = 'block';
        console.error(error);
    } finally {
        // Volta o botão ao estado normal
        loading.style.display = 'none';
        btnGerarAnalise.disabled = false;
        btnGerarAnalise.style.opacity = '1';
        btnGerarAnalise.textContent = 'Refazer Análise';
    }
}
